<?php
namespace Wandu\Database\Query;

use Wandu\Database\Query\Expression\HasWhereExpression;
use Wandu\Database\Support\Helper;

class UpdateQuery extends HasWhereExpression
{
    /** @var string */
    protected $table;

    /** @var array */
    protected $attributes;
    
    /**
     * @param string $table
     * @param array $attributes
     */
    public function __construct($table, array $attributes = [])
    {
        $this->table = $table;
        $this->attributes = $attributes;
    }

    /**
     * @param array $attributes
     * @return static
     */
    public function set(array $attributes = [])
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param array $attributes
     * @return static
     */
    public function addSet(array $attributes = [])
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        $parts = ['UPDATE `' . $this->table . '`'];
        if (count($this->attributes)) {
            $columns = array_keys($this->attributes);
            $parts[] = "SET " . Helper::arrayImplode(', ', $columns, "`", "` = ?");
        }
        if ($part = parent::toSql()) {
            $parts[] = $part;
        }
        return implode(' ', $parts);
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return array_merge(array_values($this->attributes), parent::getBindings());
    }
}
