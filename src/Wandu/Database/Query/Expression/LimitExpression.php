<?php
namespace Wandu\Database\Query\Expression;

use Wandu\Database\Query\ExpressionInterface;

class LimitExpression implements ExpressionInterface
{
    /** @var int */
    protected $take;

    /** @var int */
    protected $offset;

    /**
     * @param int $take
     * @return static
     */
    public function take($take = null)
    {
        $this->take = $take;
        return $this;
    }

    /**
     * @param int $offset
     * @return static
     */
    public function offset($offset = null)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param int $offset
     * @param int $take
     * @return static
     */
    public function limit($offset, $take)
    {
        $this->offset = $offset;
        $this->take = $take;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if (!isset($this->take) && !isset($this->offset)) {
            return '';
        }
        $sqlParts = [];
        if (isset($this->take)) {
            $sqlParts[] = "LIMIT ?";
        }
        if (isset($this->offset)) {
            $sqlParts[] = "OFFSET ?";
        }
        return implode(" ", $sqlParts);
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        $bindings = [];
        if (isset($this->take)) {
            $bindings[] = $this->take;
        }
        if (isset($this->offset)) {
            $bindings[] = $this->offset;
        }
        return $bindings;
    }
}
