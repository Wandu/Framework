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

    /** @var string */
    protected $primaryKey = 'id';

    /** @var bool */
    protected $increments = true;
    
    /** @var \Wandu\Database\Annotations\Column[] */
    protected $columns = [];
    
    /** @var \Wandu\Database\Annotations\Cast[] */
    protected $casts = [];
    
    /** @var \Wandu\Database\Annotations\RelationInterface[] */
    protected $relations = [];
    
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
     * @param string $class
     * @param array $settings
     */
    public function __construct($class, array $settings = [])
    {
        $this->class = $class;
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
     * @return \Wandu\Database\Annotations\Column[]
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
     * @return \Wandu\Database\Annotations\Cast[]
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
        foreach ($this->columns as $propertyName => $column) {
            if ($this->primaryKey === $column->name) {
                return $propertyName;
            }
        }
    }
}
