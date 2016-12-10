<?php
namespace Wandu\Database\Query;

use Wandu\Database\Contracts\QueryInterface;
use Wandu\Database\Query\Expression\HasWhereExpression;
use Wandu\Database\Support\Helper;

class SelectQuery extends HasWhereExpression implements QueryInterface
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
                $columnSqlParts[] = Helper::normalizeName($column);
            }
        }
        $parts = ["SELECT ". implode(', ', $columnSqlParts) ." FROM " . Helper::normalizeName($this->table)];
        if ($part = parent::toSql()) {
            $parts[] = $part;
        }
        return implode(' ', $parts);
    }
}
