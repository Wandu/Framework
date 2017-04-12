<?php
namespace Wandu\Database\Entity;

class Metadata
{
    /** @var string */
    protected $connection;
    
    /** @var string */
    protected $table;
    
    /** @var string */
    protected $class;
    
    /** @var array */
    protected $columns = [];
    
    /** @var array */
    protected $casts = [];
    
    /** @var \Wandu\Database\Annotations\RelationInterface[] */
    protected $relations = [];
    
    /** @var string */
    protected $primaryKey = 'id';
    
    /** @var bool */
    protected $increments = true;
    
    /**
     * @example
     * [
     *     'class' => RepositoryTestActor::class,
     *     'table' => 'actor',
     *     'columns' => [
     *         'id' => 'act_id',
     *         'firstName' => 'first_name',
     *         'lastName' => 'last_name',
     *         'lastUpdate' => 'last_update',
     *     ],
     *     'primaryKey' => 'id',
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
    public function getConnection(): string
    {
        return $this->connection;
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
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return \Wandu\Database\Annotations\RelationInterface[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
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

    /**
     * @return string
     */
    public function getPrimaryProperty()
    {
        foreach ($this->columns as $propertyName => $columnName) {
            if ($this->primaryKey === $columnName) {
                return $propertyName;
            }
        }
    }
}
