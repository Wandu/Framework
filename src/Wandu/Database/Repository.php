<?php
namespace Wandu\Database;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use Wandu\Caster\CastManagerInterface;
use Wandu\Collection\ArrayList;
use Wandu\Collection\Contracts\ListInterface;
use Wandu\Database\Contracts\Connection;
use Wandu\Database\Contracts\QueryInterface;
use Wandu\Database\Entity\Metadata;
use Wandu\Database\Exception\EntityNotFoundException;
use Wandu\Database\Query\SelectQuery;

class Repository
{
    /** @var \Wandu\Database\Contracts\Connection */
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
    
    public function __construct(Connection $connection, QueryBuilder $queryBuilder, Metadata $meta, Configuration $config)
    {
        $this->connection = $connection;
        $this->caster = $config->getCaster();
        $this->queryBuilder = $queryBuilder;
        $this->meta = $meta;
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \Generator
     */
    public function fetch($query = null, array $bindings = [])
    {
        foreach ($this->connection->fetch(...$this->normalizeSelectQuery($query, $bindings)) as $row) {
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
     * @return ?object
     */
    public function first($query = null, array $bindings = [])
    {
        $entity = $this->connection->first(...$this->normalizeSelectQuery($query, $bindings));
        if ($entity) {
            return $this->hydrate($entity);
        }
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return object
     * @throws \Wandu\Database\Exception\EntityNotFoundException
     */
    public function firstOrFail($query = null, array $bindings = [])
    {
        if ($entity = $this->first($query, $bindings)) {
            return $entity;
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
    public function persist($entity)
    {
        $this->assertIsInstance($entity, __METHOD__);
        $identifier = $this->getIdentifier($entity);
        if ($identifier) {
            return $this->executeUpdate($entity);
        }
        return $this->executeInsert($entity);
    }

    /**
     * @param object $entity
     * @return int
     */
    protected function executeInsert($entity)
    {
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
        $rowAffected = $this->query($this->queryBuilder->insert($attributesToStore));
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
    protected function executeUpdate($entity)
    {
        $primaryKey = $this->meta->getPrimaryKey(); 

        $identifier = $this->getIdentifier($entity);
        $attributesToStore = [];
        foreach ($this->meta->getColumns() as $propertyName => $column) {
            if ($primaryKey === $column->name) continue;
            $attributesToStore[$column->name] = $this->pickProperty($this->getPropertyReflection($propertyName), $entity);
        }

        return $this->query(
            $this->queryBuilder->update($attributesToStore)->where($primaryKey, $identifier)
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
        if (!$identifier) {
            return 0;
        }

        $affectedRows = $this->query($this->queryBuilder->delete()->where($primaryKey, $identifier));
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
    protected function query($query, array $bindings = [])
    {
        while (is_callable($query)) {
            $query = call_user_func($query);
        }
        if ($query instanceof QueryInterface) {
            $bindings = $query->getBindings();
            $query = $query->toSql();
        }
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
//                $value = $relations[$propertyName]->getRelation($this->manager, $attributes[$column->name]);
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
        return $identifier;
    }
    
    private function normalizeSelectQuery($query = null, array $bindings = [])
    {
        if (!isset($query) || is_callable($query)) {
            $connection = $this->connection;
            $queryBuilder = $this->queryBuilder->select();
            if (!isset($query)) {
                return $queryBuilder;
            }
            while (is_callable($query)) {
                $query = call_user_func($query, $queryBuilder, $connection);
            }
        }
        if ($query instanceof QueryInterface) {
            $bindings = $query->getBindings();
            $query = $query->toSql();
        }
        return [$query, $bindings];
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
