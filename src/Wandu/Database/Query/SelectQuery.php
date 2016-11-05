<?php
namespace Wandu\Database\Query;

class SelectQuery extends HasWhereExpression
{
    /** @var string */
    protected $table;
    
    /** @var array */
    protected $columns = ['*'];
    
    /**
     * @param string $table
     * @param array $columns
     */
    public function __construct($table, array $columns = [])
    {
        $this->table = $table;
        $this->columns = $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        $columnSqlParts = [];
        foreach ($this->columns as $key => $column) {
            if ($column === '*') {
                $columnSqlParts[] = '*';
            } else {
                $columnSqlParts[] = "`{$column}`";
            }
        }
        $parts = ["SELECT ". implode(', ', $columnSqlParts) ." FROM `{$this->table}`"];
        if ($part = parent::toSql()) {
            $parts[] = $part;
        }
        return implode(' ', $parts);
    }
}
