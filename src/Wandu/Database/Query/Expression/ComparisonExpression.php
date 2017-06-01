<?php
namespace Wandu\Database\Query\Expression;

use Wandu\Database\Contracts\ExpressionInterface;
use Wandu\Database\Support\Helper;

/**
 * ComparisonExpression = '`' $name '` ' $operator ' ' $value
 * 
 * @example `abc` = 30
 * @example `foo` > 'foo string'
 */
class ComparisonExpression implements ExpressionInterface
{
    const OPERATOR_LT = '<';
    const OPERATOR_LTE = '<=';
    const OPERATOR_GT = '>';
    const OPERATOR_GTE = '>=';
    const OPERATOR_EQ = '=';
    
    /** @var string */
    protected $name;
    
    /** @var string */
    protected $operator;
    
    /** @var array|string */
    protected $value;
    
    /**
     * WhereStatement constructor.
     * @param string $name
     * @param string $operator
     * @param string|array|\Wandu\Database\Contracts\ExpressionInterface $value
     */
    public function __construct($name, $operator, $value)
    {
        $this->name = $name;
        $this->operator = strtoupper($operator);
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        $name = Helper::normalizeName($this->name);
        $operator = $this->operator;
        if ($this->operator === 'IN') {
            if ($this->value instanceof ExpressionInterface) {
                return "{$name} {$operator} (" . $this->value->toSql() . ")";
            }
            return Helper::stringRepeat(', ', '?', count($this->value), "{$name} {$operator} (", ")");
        }
        if ($this->value instanceof ExpressionInterface) {
            return "{$name} {$operator} " . $this->value->toSql();
        }
        return "{$name} {$operator} ?";
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        $bindings = [];
        foreach (is_array($this->value) ? $this->value : [$this->value] as $value) {
            if ($value instanceof ExpressionInterface) {
                $bindings = array_merge($bindings, $value->getBindings());
            } else {
                $bindings[] = $value;
            }
        }
        return $bindings;
    }
}
