<?php
namespace Wandu\Database;

use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\GenerateOnInsert;
use Wandu\Database\Annotations\Identifier;
use Wandu\Database\Annotations\Table;
use Wandu\Database\Contracts\ConnectionInterface;

class Repository
{
    /** @var \Wandu\Database\Contracts\ConnectionInterface */
    protected $connection;
    
    /** @var \ReflectionClass */
    protected $class;
    
    /** @var \ReflectionProperty[] key-value property reflections */
    protected $properties;
    
    /** @var \Wandu\Database\Annotations\Table */
    protected $table;
    
    /** @var \Wandu\Database\Annotations\Column[] */
    protected $columns = [];
    
    /** @var string */
    protected $identifier;

    /** @var string */
    protected $generateOnInsert;
    
    /**
     * @param \Wandu\Database\Contracts\ConnectionInterface $connection
     * @param \Doctrine\Common\Annotations\Reader $reader
     * @param string $model
     */
    public function __construct(ConnectionInterface $connection, Reader $reader, $model)
    {
        $this->connection = $connection;
        
        $this->class = $reflClass = new ReflectionClass($model);
        $this->properties = [];
        $reflProperties = $reflClass->getProperties();

        class_exists(Table::class);
        class_exists(Column::class);
        class_exists(Identifier::class);
        class_exists(GenerateOnInsert::class);

        $this->table = $reader->getClassAnnotation($reflClass, Table::class);

        $columns = [];
        $identifierPropertyName = null;
        $generateOnInsertPropertyName = null;
        foreach ($reflProperties as $reflProperty) {
            $propertyName = $reflProperty->getName();
            $this->properties[$propertyName] = $reflProperty;
            $propertyAnnotations = $reader->getPropertyAnnotations($reflProperty);
            foreach ($propertyAnnotations as $annotation) {
                if ($annotation instanceof Column) {
                    $columns[$propertyName] = $annotation;
                } elseif ($annotation instanceof Identifier) {
                    $this->identifier = $propertyName;
                } elseif ($annotation instanceof GenerateOnInsert) {
                    $this->generateOnInsert = $propertyName;
                }
            }
        }
        $this->columns = $columns;
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \Generator
     */
    public function fetch($query, array $bindings = [])
    {
        foreach ($this->connection->fetch($query, $bindings) as $row) {
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
        return $this->hydrate($this->connection->first($query, $bindings));
    }
    
    public function store($entity, $table)
    {
        if (!$this->class->isInstance($entity)) {
            throw new InvalidArgumentException(
                "Argument 1 passed to " . __METHOD__ . "() must be of the type " . $this->class->getName()
            );
        }
        $identifierKey = null;
        $attributesToStore = [];
        foreach ($this->columns as $propertyName => $column) {
            if ($this->generateOnInsert === $propertyName) continue;
            $attributesToStore[$column->name] = $this->pickProperty($this->properties[$propertyName], $entity);
        }
        $this->query($this->connection->createQueryBuilder($table)->insert($attributesToStore));
        if ($this->generateOnInsert) {
            $lastInsertId = $this->connection->getLastInsertId();
            $this->injectProperty($this->properties[$this->generateOnInsert], $entity, $lastInsertId);
        }
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return bool
     */
    public function query($query, array $bindings = [])
    {
        return $this->connection->query($query, $bindings);
    }

    /**
     * @param array $row
     * @return object
     */
    public function hydrate(array $row = [])
    {
        $entity = $this->class->newInstanceWithoutConstructor();
        foreach ($this->columns as $propertyName => $column) {
            $value = $this->cast($row[$column->name], $column->cast);
            $this->injectProperty($this->properties[$propertyName], $entity, $value);
        }
        return $entity;
    }

    private function cast($value, $type)
    {
        // {"string", "integer", "float", "boolean", "array", "datetime", "date", "time"}
        switch ($type) {
            case 'string':
                return (string) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'boolean':
                return (bool) $value;
//            case 'object':
//                return $this->fromJson($value, true);
//            case 'array':
//            case 'json':
//                return $this->fromJson($value);
//            case 'collection':
//                return new BaseCollection($this->fromJson($value));
//            case 'date':
//            case 'datetime':
//                return $this->asDateTime($value);
//            case 'timestamp':
//                return $this->asTimeStamp($value);
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
}
