<?php
namespace Wandu\Database\Query;

use Wandu\Database\Contracts\ExpressionInterface;
use Wandu\Database\Query\Expression\LimitExpression;
use Wandu\Database\Query\Expression\OrderByExpression;
use Wandu\Database\Query\Expression\WhereExpression;

class HasWhereExpression implements ExpressionInterface
{
    /** @var \Wandu\Database\Query\Expression\WhereExpression */
    protected $where;

    /** @var \Wandu\Database\Query\Expression\OrderByExpression */
    protected $orderBy;

    /** @var \Wandu\Database\Query\Expression\LimitExpression */
    protected $limit;

    /**
     * @param string|array|callable $name
     * @param string $operator
     * @param string $value
     * @return static
     */
    public function where($name, $operator = null, $value = null)
    {
        if (!isset($this->where)) {
            $this->where = new WhereExpression();
        }
        $this->where->where($name, $operator, $value);
        return $this;
    }

    /**
     * @param string|array|callable $name
     * @param string $operator
     * @param string $value
     * @return static
     */
    public function andWhere($name, $operator = null, $value = null)
    {
        if (!isset($this->where)) {
            $this->where = new WhereExpression();
        }
        $this->where->andWhere($name, $operator, $value);
        return $this;
    }

    /**
     * @param string|array|callable $name
     * @param string $operator
     * @param string $value
     * @return static
     */
    public function orWhere($name, $operator = null, $value = null)
    {
        if (!isset($this->where)) {
            $this->where = new WhereExpression();
        }
        $this->where->orWhere($name, $operator, $value);
        return $this;
    }

    /**
     * @param array|string $name
     * @param boolean $asc
     * @return static
     */
    public function orderBy($name, $asc = true)
    {
        if (!isset($this->orderBy)) {
            $this->orderBy = new OrderByExpression();
        }
        $this->orderBy->orderBy($name, $asc);
        return $this;
    }

    /**
     * @param array|string $name
     * @param boolean $asc
     * @return static
     */
    public function andOrderBy($name, $asc = true)
    {
        if (!isset($this->orderBy)) {
            $this->orderBy = new OrderByExpression();
        }
        $this->orderBy->andOrderBy($name, $asc);
        return $this;
    }

    /**
     * @param int $take
     * @return static
     */
    public function take($take = null)
    {
        if (!isset($this->limit)) {
            $this->limit = new LimitExpression();
        }
        $this->limit->take($take);
        return $this;
    }

    /**
     * @param int $offset
     * @return static
     */
    public function offset($offset = null)
    {
        if (!isset($this->limit)) {
            $this->limit = new LimitExpression();
        }
        $this->limit->offset($offset);
        return $this;
    }

    /**
     * @param int $offset
     * @param int $take
     * @return static
     */
    public function limit($offset, $take)
    {
        if (!isset($this->limit)) {
            $this->limit = new LimitExpression();
        }
        $this->limit->limit($offset, $take);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        $parts = array_reduce([
            $this->where,
            $this->orderBy,
            $this->limit,
        ], function ($carry, $expression) {
            /* @var \Wandu\Database\Contracts\ExpressionInterface $expression */
            if (isset($expression) && $sqlPart = $expression->toSql()) {
                $carry[] = $sqlPart;
                return $carry;
            }
            return $carry;
        }, []);
        return implode(' ', $parts);
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return array_reduce([
            $this->where,
            $this->orderBy,
            $this->limit,
        ], function ($carry, $expression) {
            /* @var \Wandu\Database\Contracts\ExpressionInterface $expression */
            if (isset($expression)) {
                return array_merge($carry, $expression->getBindings());
            }
            return $carry;
        }, []);
    }
}
