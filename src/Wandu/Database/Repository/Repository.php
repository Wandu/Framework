<?php
namespace Wandu\Database\Repository;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use stdClass;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Entity\Metadata;
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
    
    /** @var \Wandu\Database\QueryBuilder */
    protected $queryBuilder;
    
    /** @var \ReflectionClass */
    protected $reflClass;
    
    /** @var \ReflectionProperty[] */
    protected $refProperties = [];
    
    /**
     * Repository constructor.
     * @param \Wandu\Database\Manager $manager
     * @param \Wandu\Database\Entity\Metadata $meta
     */
    public function __construct(Manager $manager, Metadata $meta)
    {
        $this->manager = $manager;
        $this->connection = $manager->connection($meta->getConnection());
        $this->meta = $meta;
        $this->query = new QueryBuilder($meta->getTable());
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
     * @return object
     */
    public function first($query = null, array $bindings = [])
    {
        return $this->hydrate($this->connection->first($this->normalizeSelectQuery($query), $bindings));
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
        foreach ($columns as $propertyName => $columnName) {
            if ($primaryKey === $columnName) {
                $primaryProperty = $propertyName;
                continue;
            }
            $attributesToStore[$columnName] = $this->pickProperty($this->getPropertyReflection($propertyName), $entity);
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
        foreach ($this->meta->getColumns() as $propertyName => $columnName) {
            if ($primaryKey === $columnName) continue;
            $attributesToStore[$columnName] = $this->pickProperty($this->getPropertyReflection($propertyName), $entity);
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
        $casts = $this->meta->getCasts();
        $relations = $this->meta->getRelations();
        $entity = $this->getClassReflection()->newInstanceWithoutConstructor();
        foreach ($this->meta->getColumns() as $propertyName => $column) {
            if (!isset($attributes[$column])) continue;
            if (isset($relations[$propertyName])) {
                $value = $relations[$propertyName]->getRelation($this->manager, $attributes[$column]);
            } elseif (isset($casts[$column])) {
                $value = $this->cast($attributes[$column], $casts[$column]);
            } else {
                $value = $attributes[$column];
            }
            $this->injectProperty($this->getPropertyReflection($propertyName), $entity, $value);
        }
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
    
    private function cast($value, $type)
    {
        // "string", "integer", "float", "boolean", "array", "datetime", "date", "time"
        switch ($type) {
            case 'string':
                return (string) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'boolean':
                return (bool) $value;
            default:
                return $value;
        }
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
