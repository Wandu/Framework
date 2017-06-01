<?php
namespace Wandu\Database\Query\Expression;

use Traversable;
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
        $value = $this->value;
        if ($this->operator === 'IN') {
            if ($value instanceof ExpressionInterface) {
                return "{$name} {$operator} (" . $value->toSql() . ")";
            }
            if ($value instanceof Traversable) {
                $value = iterator_to_array($value);
            }
            return Helper::stringRepeat(', ', '?', count($value), "{$name} {$operator} (", ")");
        }
        if ($value instanceof ExpressionInterface) {
            return "{$name} {$operator} " . $value->toSql();
        }
        return "{$name} {$operator} ?";
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        $bindings = [];
        $values = (is_array($this->value) || $this->value instanceof Traversable) ? $this->value : [$this->value];
        foreach ($values as $value) {
            if ($value instanceof ExpressionInterface) {
                $bindings = array_merge($bindings, $value->getBindings());
            } else {
                $bindings[] = $value;
            }
        }
        return $bindings;
    }
}
