<?php
namespace Wandu\Database\Query\Expression;

use Wandu\Database\Contracts\ExpressionInterface;

/**
 * LogicalExpression = '(' ComparisonExpression (' AND '|' OR ') LogicalExpression ')' | ComparisonExpression
 *
 * @example `abc` = 30
 * @example (`abc` = 30 AND `foo` = 30 OR `foo` = 40)
 */
class LogicalExpression implements ExpressionInterface
{
    /** @var array */
    protected $operators = [];
    
    /** @var \Wandu\Database\Contracts\ExpressionInterface[] */
    protected $expressions = [];

    /**
     * @param string|array|callable $name
     * @param string $operator
     * @param string $value
     * @return static
     */
    public function where($name, $operator = null, $value = null)
    {
        return $this->andWhere($name, $operator, $value);
    }

    /**
     * @param string|array|callable $name
     * @param string $operator
     * @param string $value
     * @return static
     */
    public function andWhere($name, $operator = null, $value = null)
    {
        $this->addWhereQuery('AND', $name, $operator, $value);
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
        $this->addWhereQuery('OR', $name, $operator, $value);
        return $this;
    }

    protected function addWhereQuery($logicalOp, $name, $operator = null, $value = null)
    {
        if ($name instanceof ExpressionInterface) {
            $this->operators[] = $logicalOp;
            $this->expressions[] = $name;
        } elseif (!is_string($name) && is_callable($name)) {
            $newWhere = new LogicalExpression();
            $newWhereResult = call_user_func($name, $newWhere);
            $this->operators[] = $logicalOp;
            $this->expressions[] = $newWhereResult ?: $newWhere;
        } elseif (!is_array($name)) {
            $this->operators[] = $logicalOp;
            $this->expressions[] = new ComparisonExpression($name, $operator, $value);
        } else {
            foreach ($name as $column => $value) {
                if (is_array($value)) {
                    $newWhere = new LogicalExpression();
                    foreach ($value as $innerOperator => $innerValue) {
                        $newWhere->andWhere($column, $innerOperator, $innerValue);
                    }
                    $this->operators[] = $logicalOp;
                    $this->expressions[] = $newWhere;
                } else {
                    $this->operators[] = $logicalOp;
                    $this->expressions[] = new ComparisonExpression($column, '=', $value);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        $sql = '';
        foreach ($this->expressions as $index => $expression) {
            if ($index) {
                $sql .= ' ' . $this->operators[$index] . ' ';
            }
            $sql .= $expression->toSql();
        }
        if (count($this->expressions) > 1) {
            $sql = "(" . $sql . ")";
        }
        return $sql;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return array_reduce($this->expressions, function ($carry, ExpressionInterface $expression) {
            return array_merge($carry, $expression->getBindings());
        }, []);
    }
}
