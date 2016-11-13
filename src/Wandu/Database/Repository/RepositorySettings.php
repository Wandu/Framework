<?php
namespace Wandu\Database\Repository;

use Doctrine\Common\Annotations\Reader;

class RepositorySettings
{
    public static function fromAnnotation($model, Reader $reader = null)
    {
//        $this->connection = $connection;
//
//        $this->class = $reflClass = new ReflectionClass($model);
//        $this->properties = [];
//        $reflProperties = $reflClass->getProperties();
//
//        class_exists(Table::class);
//        class_exists(Column::class);
//        class_exists(AutoIncrements::class);
//
//        $this->table = $reader->getClassAnnotation($reflClass, Table::class);
//
//        $columns = [];
//        $identifierPropertyName = null;
//        $generateOnInsertPropertyName = null;
//        foreach ($reflProperties as $reflProperty) {
//            $propertyName = $reflProperty->getName();
//            $this->properties[$propertyName] = $reflProperty;
//            $propertyAnnotations = $reader->getPropertyAnnotations($reflProperty);
//            foreach ($propertyAnnotations as $annotation) {
//                if ($annotation instanceof Column) {
//                    $columns[$propertyName] = $annotation;
//                } elseif ($annotation instanceof AutoIncrements) {
//                    $this->generateOnInsert = $propertyName;
//                }
//            }
//        }
//        $this->columns = $columns;        
    }
    
    /** @var string */
    protected $table;
    
    /** @var string */
    protected $model;
    
    /** @var array */
    protected $columns = [];
    
    /** @var array */
    protected $casts = [];
    
    /** @var string */
    protected $identifier = 'id';
    
    /** @var bool */
    protected $increments = true;
    
    /**
     * @example
     * [
     *     'model' => RepositoryTestActor::class,
     *     'table' => 'actor',
     *     'columns' => [
     *         'id' => 'act_id',
     *         'firstName' => 'first_name',
     *         'lastName' => 'last_name',
     *         'lastUpdate' => 'last_update',
     *     ],
     *     'identifier' => 'id',
     *     'increments' => true,
     * ]
     * @param string $table
     * @param array $settings
     */
    public function __construct($table, array $settings = [])
    {
        $this->table = $table;
        foreach ($settings as $name => $setting) {
            $this->{$name} = $setting;
        }
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return boolean
     */
    public function isIncrements()
    {
        return $this->increments;
    }

    /**
     * @return array
     */
    public function getCasts()
    {
        return $this->casts;
    }
}
