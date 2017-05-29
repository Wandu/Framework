<?php
namespace Wandu\Database\Query;

use Traversable;
use Wandu\Database\Contracts\ExpressionInterface;
use Wandu\Database\Contracts\QueryInterface;
use Wandu\Database\Support\Helper;

class InsertQuery implements QueryInterface
{
    /** @var string */
    protected $table;
    
    /** @var array */
    protected $values = [];
    
    /**
     * @param string $table
     * @param array|\Traversable $values
     */
    public function __construct($table, $values = [])
    {
        $this->table = $table;
        if (is_array($values)) {
            $this->values = $values;
        } elseif ($values instanceof Traversable) {
            $this->values = iterator_to_array($values);
        } else {
            $this->values = [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        $valuesList = $this->values;
        if (array_values($valuesList) !== $valuesList) {
            $valuesList = [$valuesList];
        }
        $fields = array_keys($valuesList[0]); // fields

        $sqlPartValuesList = [];
        foreach ($valuesList as $values) {
            $sqlPartValues = [];
            foreach ($fields as $field) {
                $value = $values[$field] ?? null;
                if ($value instanceof ExpressionInterface) {
                    $sqlPartValues[] = $value->toSql();
                } else {
                    $sqlPartValues[] = '?';
                }
            }
            $sqlPartValuesList[] = implode(", ", $sqlPartValues);
        }
        $sql = "INSERT INTO `{$this->table}`(`". implode("`, `", array_values($fields)) . "`)"
            ." VALUES " . Helper::arrayImplode(", ", $sqlPartValuesList, '(', ')');
        return $sql;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        $valuesList = $this->values;
        if (array_values($valuesList) !== $valuesList) {
            $valuesList = [$valuesList];
        }
        $fields = array_keys($valuesList[0]);
        $bindings = [];
        foreach ($valuesList as $values) {
            foreach ($fields as $field) {
                $value = $values[$field] ?? null;
                if ($value instanceof ExpressionInterface) {
                    $bindings = array_merge($bindings, $value->getBindings());
                } else {
                    $bindings[] = $value;
                }
            }
        }
        return $bindings;
    }
}
