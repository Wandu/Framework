<?php
namespace Wandu\Database\Repository;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\Table;

class RepositorySettings
{
    public static function fromAnnotation($model, Reader $reader)
    {
        $settings = [
            'model' => $model,
        ];
        $classRefl = new ReflectionClass($model);
        $propertiesRefl = $classRefl->getProperties();

        class_exists(Table::class);
        class_exists(Column::class);

        /* @var \Wandu\Database\Annotations\Table $table */
        if ($table = $reader->getClassAnnotation($classRefl, Table::class)) {
            $settings['identifier'] = $table->identifier;
            $settings['increments'] = $table->increments;
        }
        
        $columns = [];
        $casts = [];
        foreach ($propertiesRefl as $propertyRefl) {
            /* @var \Wandu\Database\Annotations\Column $column */
            if ($column = $reader->getPropertyAnnotation($propertyRefl, Column::class)) {
                $columns[$column->name] = $propertyRefl->name;
                $casts[$column->name] = $column->cast;
            }
        }
        if (count($columns)) {
            $settings['columns'] = $columns;
        }
        if (count($casts)) {
            $settings['casts'] = $casts;
        }
        return new RepositorySettings($table->name, $settings);
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
