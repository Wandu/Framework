<?php
namespace Wandu\Database\Query\Expression;

use Wandu\Database\Query\ExpressionInterface;

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
     * @param string|array $value
     */
    public function __construct($name, $operator, $value)
    {
        $this->name = $name;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        return "`{$this->name}` {$this->operator} ?";
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return is_array($this->value) ? $this->value : [$this->value];
    }
}
