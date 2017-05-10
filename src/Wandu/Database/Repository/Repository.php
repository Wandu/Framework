<?php
namespace Wandu\Database\Repository;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use Wandu\Caster\CastManagerInterface;
use Wandu\Collection\ArrayList;
use Wandu\Collection\Contracts\ListInterface;
use Wandu\Database\Entity\Metadata;
use Wandu\Database\Exception\EntityNotFoundException;
use Wandu\Database\Exception\IdentifierNotFoundException;
use Wandu\Database\Manager;
use Wandu\Database\Query\SelectQuery;
use Wandu\Database\QueryBuilder;

class Repository
{
    /** @var \Wandu\Database\Manager */
    protected $manager;
    
    /** @var \Wandu\Database\Contracts\ConnectionInterface */
    protected $connection;
    
    /** @var \Wandu\Database\Entity\Metadata */
    protected $meta;
    
    /** @var \Wandu\Caster\CastManagerInterface */
    protected $caster;
    
    /** @var \Wandu\Database\QueryBuilder */
    protected $queryBuilder;
    
    /** @var \ReflectionClass */
    protected $reflClass;
    
    /** @var \ReflectionProperty[] */
    protected $refProperties = [];
    
    /** @var array */
    protected static $entityCache = [];
    
    public function __construct(Manager $manager, Metadata $meta, CastManagerInterface $caster)
    {
        $this->manager = $manager;
        $this->connection = $manager->connection($meta->getConnection());
        $this->meta = $meta;
        $this->caster = $caster;
        $this->query = new QueryBuilder($this->connection->getConfig()->getPrefix() . $meta->getTable());
    }

    /**
     * @return \Wandu\Database\Entity\Metadata
     */
    public function getMeta(): Metadata
    {
        return $this->meta;
    }
    
    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \Generator
     */
    public function fetch($query = null, array $bindings = [])
    {
        foreach ($this->connection->fetch($this->normalizeSelectQuery($query), $bindings) as $row) {
            yield $this->hydrate($row);
        }
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function all($query = null, array $bindings = []): ListInterface
    {
        return new ArrayList($this->fetch($query, $bindings));
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return object
     */
    public function first($query = null, array $bindings = [])
    {
        return $this->hydrate($this->connection->first($this->normalizeSelectQuery($query), $bindings));
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return object
     * @throws \Wandu\Database\Exception\EntityNotFoundException
     */
    public function firstOrFail($query = null, array $bindings = [])
    {
        $attributes = $this->connection->first($this->normalizeSelectQuery($query), $bindings);
        if (isset($attributes)) {
            return $this->hydrate($attributes);
        }
        throw new EntityNotFoundException();
    }

    /**
     * @param string|int $identifier
     * @return object
     */
    public function find($identifier)
    {
        return $this->first(function (SelectQuery $select) use ($identifier) {
            return $select->where($this->meta->getPrimaryKey(), $identifier);
        });
    }

    /**
     * @param string|int $identifier
     * @return object
     * @throws \Wandu\Database\Exception\EntityNotFoundException
     */
    public function findOrFail($identifier)
    {
        return $this->firstOrFail(function (SelectQuery $select) use ($identifier) {
            return $select->where($this->meta->getPrimaryKey(), $identifier);
        });
    }

    /**
     * @param array $identifiers
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function findMany(array $identifiers = []): ListInterface
    {
        return $this->all(function (SelectQuery $select) use ($identifiers) {
            return $select->where($this->meta->getPrimaryKey(), 'IN', $identifiers);
        });
    }
    
    /**
     * @param object $entity
     * @return int
     */
    public function insert($entity)
    {
        $this->assertIsInstance($entity, __METHOD__);
        $primaryKey = $this->meta->getPrimaryKey();
        $primaryProperty = null;
        $columns = $this->meta->getColumns();
        $attributesToStore = [];
        foreach ($columns as $propertyName => $column) {
            if ($primaryKey === $column->name) {
                $primaryProperty = $propertyName;
                continue;
            }
            $attributesToStore[$column->name] = $this->pickProperty($this->getPropertyReflection($propertyName), $entity);
        }
        $rowAffected = $this->query($this->query->insert($attributesToStore));
        if ($this->meta->isIncrements()) {
            $lastInsertId = $this->connection->getLastInsertId();
            if ($primaryProperty) {
                $this->injectProperty($this->getPropertyReflection($primaryProperty), $entity, $lastInsertId);
            }
        }
        return $rowAffected;
    }

    /**
     * @param object $entity
     * @return int
     */
    public function update($entity)
    {
        $this->assertIsInstance($entity, __METHOD__);
        $primaryKey = $this->meta->getPrimaryKey(); 

        $identifier = $this->getIdentifier($entity);
        $attributesToStore = [];
        foreach ($this->meta->getColumns() as $propertyName => $column) {
            if ($primaryKey === $column->name) continue;
            $attributesToStore[$column->name] = $this->pickProperty($this->getPropertyReflection($propertyName), $entity);
        }

        return $this->query(
            $this->query->update($attributesToStore)->where($primaryKey, $identifier)
        );
    }

    /**
     * @param object $entity
     * @return int
     */
    public function delete($entity)
    {
        $this->assertIsInstance($entity, __METHOD__);
        
        $primaryKey = $this->meta->getPrimaryKey();
        $identifier = $this->getIdentifier($entity);

        $affectedRows = $this->query($this->query->delete()->where($primaryKey, $identifier));
        if ($identifierProperty = $this->meta->getPrimaryProperty()) {
            $this->injectProperty($this->getPropertyReflection($identifierProperty), $entity, null);
        }
        return $affectedRows;
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return int
     */
    public function query($query, array $bindings = [])
    {
        return $this->connection->query($query, $bindings);
    }

    /**
     * @param array $attributes
     * @return object
     */
    public function hydrate(array $attributes = null)
    {
        if (!$attributes) {
            return null;
        }
        $entityCacheId = $this->meta->getClass() . '#' . $attributes[$this->meta->getPrimaryKey()];
        if (isset(static::$entityCache[$entityCacheId])) return static::$entityCache[$entityCacheId];

        $casts = $this->meta->getCasts();
        $relations = $this->meta->getRelations();
        $entity = $this->getClassReflection()->newInstanceWithoutConstructor();

        static::$entityCache[$entityCacheId] = $entity;

        foreach ($this->meta->getColumns() as $propertyName => $column) {
            if (!isset($attributes[$column->name])) continue;
            if (isset($relations[$propertyName])) {
                $value = $relations[$propertyName]->getRelation($this->manager, $attributes[$column->name]);
            } elseif (isset($casts[$propertyName])) {
                $value = $this->caster->cast($attributes[$column->name], $casts[$propertyName]->type);
            } else {
                $value = $attributes[$column->name];
            }
            $this->injectProperty($this->getPropertyReflection($propertyName), $entity, $value);
        }

        unset(static::$entityCache[$entityCacheId]);
        return $entity;
    }

    /**
     * @param mixed $entity
     * @return string|int
     */
    private function getIdentifier($entity)
    {
        $identifier = null;
        if ($identifierProperty = $this->meta->getPrimaryProperty()) {
            $identifier = $this->pickProperty($this->getPropertyReflection($identifierProperty), $entity);
        }
        if (!$identifier) {
            throw new IdentifierNotFoundException();
        }
        return $identifier;
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @return string|\Wandu\Database\Contracts\QueryInterface
     */
    private function normalizeSelectQuery($query = null)
    {
        if (!isset($query) || is_callable($query)) {
            $connection = $this->connection;
            $queryBuilder = $this->query->select();
            if (!isset($query)) {
                return $queryBuilder;
            }
            while (is_callable($query)) {
                $query = call_user_func($query, $queryBuilder, $connection);
            }
        }
        return $query;
    }
    
    /**
     * @param \ReflectionProperty $property
     * @param object $object
     * @param mixed $target
     */
    private function injectProperty(ReflectionProperty $property, $object, $target)
    {
        $property->setAccessible(true);
        $property->setValue($object, $target);
    }

    /**
     * @param \ReflectionProperty $property
     * @param object $object
     * @return mixed
     */
    private function pickProperty(ReflectionProperty $property, $object)
    {
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    /**
     * @param string $name
     * @return \ReflectionProperty
     */
    private function getPropertyReflection($name): ReflectionProperty
    {
        if (!isset($this->refProperties[$name])) {
            return $this->refProperties[$name] = $this->getClassReflection()->getProperty($name);
        }
        return $this->refProperties[$name];
    }

    /**
     * @return \ReflectionClass
     */
    private function getClassReflection(): ReflectionClass
    {
        if (!isset($this->reflClass)) {
            return $this->reflClass = new ReflectionClass($this->meta->getClass());
        }
        return $this->reflClass;
    }

    /**
     * @param mixed $entity
     * @param string $method
     */
    private function assertIsInstance($entity, $method)
    {
        $class = $this->meta->getClass();
        if (!$entity instanceof $class) {
            throw new InvalidArgumentException(
                "Argument 1 passed to {$method}() must be of the type " . $class
            );
        }
    }
}
