<?php
namespace Wandu\Database\Repository;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use stdClass;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Exception\IdentifierNotFoundException;

class Repository
{
    /** @var \Wandu\Database\Contracts\ConnectionInterface */
    protected $connection;
    
    /** @var \Wandu\Database\Repository\RepositorySettings */
    protected $settings;
    
    /**
     * Repository constructor.
     * @param \Wandu\Database\Contracts\ConnectionInterface $connection
     * @param \Wandu\Database\Repository\RepositorySettings $settings
     */
    public function __construct(ConnectionInterface $connection, RepositorySettings $settings)
    {
        $this->connection = $connection;
        $this->settings = $settings;
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \Generator
     */
    public function fetch($query, array $bindings = [])
    {
        foreach ($this->connection->fetch($this->normalizeQuery($query), $bindings) as $row) {
            yield $this->hydrate($row);
        }
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return object
     */
    public function first($query, array $bindings = [])
    {
        return $this->hydrate($this->connection->first($this->normalizeQuery($query), $bindings));
    }

    /**
     * @param object $entity
     * @return int
     */
    public function insert($entity)
    {
        $this->assertIsInstance($entity, __METHOD__);
        $identifierKey = $this->settings->getIdentifier();
        $columns = $this->settings->getColumns();
        $attributesToStore = [];
        foreach ($columns as $columnName => $propertyName) {
            if ($identifierKey === $propertyName) continue;
            $attributesToStore[$columnName] = $this->pickProperty($this->getPropertyReflection($propertyName), $entity);
        }
        $queryBuilder = $this->connection->createQueryBuilder($this->settings->getTable());
        $rowAffected = $this->query($queryBuilder->insert($attributesToStore));
        if ($this->settings->isIncrements()) {
            $lastInsertId = $this->connection->getLastInsertId();
            $this->injectProperty($this->getPropertyReflection($columns[$identifierKey]), $entity, $lastInsertId);
        }
        return $rowAffected;
    }

    public function update($entity)
    {
        $this->assertIsInstance($entity, __METHOD__);
        $identifierKey = $this->settings->getIdentifier();
        $columns = $this->settings->getColumns();
        $identifier = $this->pickProperty($this->getPropertyReflection($columns[$identifierKey]), $entity);
        if (!$identifier) {
            throw new IdentifierNotFoundException();
        }
        $attributesToStore = [];
        foreach ($columns as $columnName => $propertyName) {
            if ($identifierKey === $propertyName) continue;
            $attributesToStore[$columnName] = $this->pickProperty($this->getPropertyReflection($propertyName), $entity);
        }

        $queryBuilder = $this->connection->createQueryBuilder($this->settings->getTable());
        return $this->query($queryBuilder->update($attributesToStore)->where($identifierKey, $identifier));
    }

    /**
     * @param object $entity
     * @return int
     */
    public function delete($entity)
    {
        $this->assertIsInstance($entity, __METHOD__);
        $identifierKey = $this->settings->getIdentifier();
        $columns = $this->settings->getColumns();
        
        $identifier = $this->pickProperty($this->getPropertyReflection($columns[$identifierKey]), $entity);
        if (!$identifier) {
            throw new IdentifierNotFoundException();
        }
        $queryBuilder = $this->connection->createQueryBuilder($this->settings->getTable());
        $affectedRows = $this->query($queryBuilder->delete()->where($identifierKey, $identifier));
        $this->injectProperty($this->getPropertyReflection($columns[$identifierKey]), $entity, null);
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
    public function hydrate(array $attributes = [])
    {
        $model = $this->settings->getModel();
        $casts = $this->settings->getCasts();
        $columns = $this->settings->getColumns(); // map

        if ($model) {
            $classReflection = $this->getClassReflection();
            $entity = $classReflection->newInstanceWithoutConstructor();
            foreach ($attributes as $name => $attribute) {
                $value = isset($casts[$name]) ? $this->cast($attribute, $casts[$name]) : $attribute;
                $this->injectProperty($this->getPropertyReflection($columns[$name]), $entity, $value);
            }
        } else {
            $entity = new stdClass();
            foreach ($attributes as $name => $attribute) {
                $value = isset($casts[$name]) ? $this->cast($attribute, $casts[$name]) : $attribute;
                $entity->{$columns[$name]} = $value;
            }
        }
        return $entity;
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @return string|\Wandu\Database\Contracts\QueryInterface
     */
    private function normalizeQuery($query)
    {
        if (is_callable($query)) {
            $connection = $this->connection;
            $queryBuilder = $connection->createQueryBuilder($this->settings->getTable())->select();
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
     * @return \ReflectionClass
     */
    private function getClassReflection()
    {
        static $reflection;
        if (!isset($reflection)) {
            $model = $this->settings->getModel();
            return $reflection = new ReflectionClass($model ? $model : 'stdClass');
        }
        return $reflection;
    }

    /**
     * @param string $name
     * @return \ReflectionProperty
     */
    private function getPropertyReflection($name)
    {
        static $reflections = [];
        if (!isset($reflections[$name])) {
            return $reflections[$name] = $this->getClassReflection()->getProperty($name);
        }
        return $reflections[$name];
    }

    /**
     * @param mixed $entity
     * @param string $method
     */
    private function assertIsInstance($entity, $method)
    {
        if (!$this->isInstance($entity)) {
            throw new InvalidArgumentException(
                "Argument 1 passed to {$method}() must be of the type " . $this->getClassReflection()->getName()
            );
        }
    }
    
    /**
     * @param mixed $entity
     * @return boolean
     */
    private function isInstance($entity)
    {
        return $this->getClassReflection()->isInstance($entity);
    }
}
