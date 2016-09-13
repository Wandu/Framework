<?php
namespace Wandu\Database\Schema;

class RawExpression implements ExpressionInterface 
{
    /** @var string */
    protected $expression;
    
    /**
     * @param string $expression
     */
    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->expression;
    }
}
