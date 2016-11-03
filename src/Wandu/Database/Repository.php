<?php
namespace Wandu\Database;

use Doctrine\Common\Annotations\Reader;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\Identifier;
use Wandu\Database\Contracts\ConnectionInterface;
use ReflectionClass;
use ReflectionProperty;

class Repository
{
    /** @var \Wandu\Database\Contracts\ConnectionInterface */
    protected $connection;
    
    /** @var \ReflectionClass */
    protected $class;
    
    /** @var \ReflectionProperty[] key-value property reflections */
    protected $properties;
    
    /** @var array */
    protected $classAnnotations;
    
    /** @var array */
    protected $propertiesAnnotations;
    
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

        class_exists(Column::class);
        class_exists(Identifier::class);

        $this->classAnnotations = $reader->getClassAnnotations($reflClass);
        foreach ($reflProperties as $reflProperty) {
            $this->properties[$reflProperty->getName()] = $reflProperty;
            $propertyAnnotations = $reader->getPropertyAnnotations($reflProperty);
            if (count($propertyAnnotations)) {
                $this->propertiesAnnotations[$reflProperty->name] = $propertyAnnotations;
            }
        }
    }

    /**
     * @param string|callable|\Wandu\Database\Query\QueryBuilder $query
     * @param array $bindings
     * @return \Generator
     */
    public function fetch($query, array $bindings = [])
    {
//        if (is_callable($query)) {
//            $query = call_user_func($query, $this->connection->createQueryBuilder());
//        }
        foreach ($this->connection->fetch($query, $bindings) as $row) {
            yield $this->hydrate($row);
        }
    }

    /**
     * @param string|callable|\Wandu\Database\Query\QueryBuilder $query
     * @param array $bindings
     * @return object
     */
    public function first($query, array $bindings = [])
    {
//        if (is_callable($query)) {
//            $query = call_user_func($query, $this->connection->createQueryBuilder());
//        }
        return $this->hydrate($this->connection->first($query, $bindings));
    }

    /**
     * @param string|callable|\Wandu\Database\Query\QueryBuilder $query
     * @param array $bindings
     * @return bool
     */
    public function query($query, array $bindings = [])
    {
//        if (is_callable($query)) {
//            $query = call_user_func($query, $this->connection->createQueryBuilder());
//        }
        return $this->connection->query($query, $bindings);
    }

    /**
     * @param array $row
     * @return object
     */
    public function hydrate(array $row = [])
    {
        $object = $this->class->newInstanceWithoutConstructor();
        foreach ($this->propertiesAnnotations as $name => $annotations) {
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Column) {
                    $this->injectProperty($this->properties[$name], $object, $row[$annotation->name]);
                }
            }
        }
        return $object;
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
}
