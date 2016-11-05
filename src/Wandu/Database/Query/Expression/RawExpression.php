<?php
namespace Wandu\Database\Query;

use Wandu\Database\Contracts\ExpressionInterface;

class RawExpression implements ExpressionInterface 
{
    /** @var string */
    protected $expression;
    
    /** @var array */
    protected $bindings;
    
    /**
     * @param string $expression
     * @param array $bindings
     */
    public function __construct($expression, array $bindings = [])
    {
        $this->expression = $expression;
        $this->bindings = $bindings;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        return $this->expression;
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }
}
