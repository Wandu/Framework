<?php
namespace Wandu\Database\Query;

use Wandu\Database\Query\Expression\LimitExpression;
use Wandu\Database\Query\Expression\OrderByExpression;
use Wandu\Database\Query\Expression\SetExpression;
use Wandu\Database\Query\Expression\WhereExpression;
use Wandu\Database\Support\Helper;

class QueryBuilder
{
    const TYPE_SELECT = 0;
    const TYPE_DELETE = 1;
    const TYPE_UPDATE = 2;
    const TYPE_INSERT = 3;
    
    /** @var array */
    protected static $takeable = [
        0 => ['where', 'orderBy', 'limit'],
        1 => ['where', 'orderBy', 'limit'],
        2 => ['set', 'where', 'orderBy', 'limit'],
        3 => [],
    ];

    /** @var string */
    protected $table;
    
    /** @var int */
    protected $type = 0; // static::TYPE_SELECT

    /** @var array */
    protected $select = ['*'];
    
    /** @var array */
    protected $insert = [];
    
    /** @var \Wandu\Database\Query\Expression\SetExpression */
    protected $set;
    
    /** @var \Wandu\Database\Query\Expression\WhereExpression */
    protected $where;

    /** @var \Wandu\Database\Query\Expression\OrderByExpression */
    protected $orderBy;

    /** @var \Wandu\Database\Query\Expression\LimitExpression */
    protected $limit;
    
    /**
     * @param string $table
     * @return static
     */
    public static function create($table)
    {
        return new static($table);
    }
    
    /**
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }
    
    /**
     * @param array|\Traversable $valuesToInsert
     * @return static
     */
    public function insert($valuesToInsert)
    {
        $this->type = static::TYPE_INSERT;
        $this->insert = $valuesToInsert;
        return $this;
    }

    /**
     * @param array $columns
     * @return static
     */
    public function select(array $columns = ['*'])
    {
        $this->type = static::TYPE_SELECT;
        $this->select = $columns;
        return $this;
    }

    /**
     * @param array $attributes
     * @return static
     */
    public function update(array $attributes = [])
    {
        $this->type = static::TYPE_UPDATE;
        $this->set($attributes);
        return $this;
    }

    /**
     * @return static
     */
    public function delete()
    {
        $this->type = static::TYPE_DELETE;
        return $this;
    }

    /**
     * @param array $attributes
     * @return static
     */
    public function set(array $attributes = [])
    {
        if (!isset($this->set)) {
            $this->set = new SetExpression();
        }
        $this->set->set($attributes);
        return $this;
    }

    /**
     * @param array $attributes
     * @return static
     */
    public function addSet(array $attributes = [])
    {
        if (!isset($this->set)) {
            $this->set = new SetExpression();
        }
        $this->set->addSet($attributes);
        return $this;
    }

    /**
     * @param string|array|callable $name
     * @param string $operator
     * @param string $value
     * @return static
     */
    public function where($name, $operator = null, $value = null)
    {
        if (!isset($this->where)) {
            $this->where = new WhereExpression();
        }
        $this->where->where($name, $operator, $value);
        return $this;
    }

    /**
     * @param string|array|callable $name
     * @param string $operator
     * @param string $value
     * @return static
     */
    public function andWhere($name, $operator = null, $value = null)
    {
        if (!isset($this->where)) {
            $this->where = new WhereExpression();
        }
        $this->where->andWhere($name, $operator, $value);
        return $this;
    }

    /**
     * @param string|array|callable $name
     * @param string $operator
     * @param string $value
     * @return static
     */
    public function orWhere($name, $operator = null, $value = null)
    {
        if (!isset($this->where)) {
            $this->where = new WhereExpression();
        }
        $this->where->orWhere($name, $operator, $value);
        return $this;
    }

    /**
     * @param array|string $name
     * @param boolean $asc
     * @return static
     */
    public function orderBy($name, $asc = true)
    {
        if (!isset($this->orderBy)) {
            $this->orderBy = new OrderByExpression();
        }
        $this->orderBy->orderBy($name, $asc);
        return $this;
    }

    /**
     * @param array|string $name
     * @param boolean $asc
     * @return static
     */
    public function andOrderBy($name, $asc = true)
    {
        if (!isset($this->orderBy)) {
            $this->orderBy = new OrderByExpression();
        }
        $this->orderBy->andOrderBy($name, $asc);
        return $this;
    }

    /**
     * @param int $take
     * @return static
     */
    public function take($take = null)
    {
        if (!isset($this->limit)) {
            $this->limit = new LimitExpression();
        }
        $this->limit->take($take);
        return $this;
    }

    /**
     * @param int $offset
     * @return static
     */
    public function offset($offset = null)
    {
        if (!isset($this->limit)) {
            $this->limit = new LimitExpression();
        }
        $this->limit->offset($offset);
        return $this;
    }

    /**
     * @param int $offset
     * @param int $take
     * @return static
     */
    public function limit($offset, $take)
    {
        if (!isset($this->limit)) {
            $this->limit = new LimitExpression();
        }
        $this->limit->limit($offset, $take);
        return $this;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        switch ($this->type) {
            case static::TYPE_DELETE:
                $sql = "DELETE FROM `{$this->table}`";
                break;
            case static::TYPE_UPDATE:
                $sql = "UPDATE `{$this->table}`";
                break;
            case static::TYPE_INSERT:
                $sql = $this->createInsertQuery();
                break;
            default:
                $sql = $this->createSelectQuery();
        }
        return array_reduce(static::$takeable[$this->type], function ($carry, $name) {
            /** @var \Wandu\Database\Query\ExpressionInterface $expression */
            $expression = $this->{$name};
            if (isset($expression) && $sqlPart = $expression->toSql()) {
                return $carry . ' ' . $sqlPart;
            }
            return $carry;
        }, $sql);
    }

    /**
     * @return string
     */
    public function createInsertQuery()
    {
        $insertParts = $this->insert;
        if (array_values($insertParts) !== $insertParts) {
            $insertParts = [$insertParts];
        }
        $columns = array_keys($insertParts[0]);
        $valueSqlPart = Helper::stringRepeat(', ', '?', count($columns), '(', ')');
        return "INSERT INTO `{$this->table}`(`". implode("`, `", array_values($columns)) . "`)" .
            " VALUES ". Helper::stringRepeat(', ', $valueSqlPart, count($insertParts));
    }

    /**
     * @return string
     */
    protected function createSelectQuery()
    {
        $columnSqlParts = [];
        foreach ($this->select as $key => $column) {
            if ($column === '*') {
                $columnSqlParts[] = '*';
            } else {
                $columnSqlParts[] = "``{$column}``";
            }
        }
        return "SELECT ". implode(', ', $columnSqlParts) ." FROM `{$this->table}`";
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        if ($this->type === static::TYPE_INSERT) {
            return $this->getInsertBindings();
        }
        return array_reduce(static::$takeable[$this->type], function ($carry, $name) {
            /** @var \Wandu\Database\Query\ExpressionInterface $expression */
            $expression = $this->{$name};
            if (isset($expression)) {
                return array_merge($carry, $expression->getBindings());
            }
            return $carry;
        }, []);
    }

    /**
     * @return array
     */
    protected function getInsertBindings()
    {
        $insertParts = $this->insert;
        if (array_values($insertParts) !== $insertParts) {
            $insertParts = [$insertParts];
        }
        $columns = array_keys($insertParts[0]);
        $bindings = [];
        foreach ($insertParts as $insertPart) {
            foreach ($columns as $column) {
                $bindings[] = isset($insertPart[$column]) ? $insertPart[$column] : null;
            }
        }
        return $bindings;
    }
}
