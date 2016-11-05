<?php
namespace Wandu\Database\Query;

use Wandu\Database\Support\Helper;

class InsertQuery
{
    /** @var string */
    protected $table;
    
    /** @var array */
    protected $values = [];
    
    /**
     * @param string $table
     * @param array|\Traversable $values
     */
    public function __construct($table, $values)
    {
        $this->table = $table;
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        $insertParts = $this->values;
        if (array_values($insertParts) !== $insertParts) {
            $insertParts = [$insertParts];
        }
        $columns = array_keys($insertParts[0]);
        $valueSqlPart = Helper::stringRepeat(', ', '?', count($columns), '(', ')');
        return "INSERT INTO `{$this->table}`(`". implode("`, `", array_values($columns)) . "`)" .
            " VALUES ". Helper::stringRepeat(', ', $valueSqlPart, count($insertParts));
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        $insertParts = $this->values;
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
