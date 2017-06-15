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
    /** @var array */
    protected static $ops = [
        '<',
        '>',
        '>=',
        '<=',
        '=',
        '!=',
        '<>',
        '<=>',
        'IS',
        'IS NOT',
        'IS NOT NULL',
        'IS NULL',
        'LIKE',
        'NOT LIKE',
    ];
    
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
    public function __construct($name, $operator = null, $value = null)
    {
        if (!isset($value)) {
            if (!isset($operator)) {
                $operator = 'IS NULL';
            } else {
                $upperOp = strtoupper($operator);
                if (in_array($upperOp, static::$ops)) {
                    if ($upperOp === '=' || $upperOp === 'IS') $operator = 'IS NULL';
                    if ($upperOp === '!=' || $upperOp === 'IS NOT') $operator = 'IS NOT NULL';
                } else {
                    $value = $operator;
                    $operator = '=';
                }
            }
        }
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
        if (isset($value)) {
            return "{$name} {$operator} ?";
        } else {
            return "{$name} {$operator}";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {

        if (is_array($this->value) || $this->value instanceof Traversable) {
            $bindings = [];
            foreach ($this->value as $value) {
                if ($value instanceof ExpressionInterface) {
                    $bindings = array_merge($bindings, $value->getBindings());
                } else {
                    $bindings[] = $value;
                }
            }
            return $bindings;
        }
        if (isset($this->value)) {
            if ($this->value instanceof ExpressionInterface) {
                return $this->value->getBindings();
            }
            return [$this->value];
        }
        return [];
    }
}
