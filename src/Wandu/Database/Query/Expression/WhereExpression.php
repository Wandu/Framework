<?php
namespace Wandu\Database\Query\Expression;

/**
 * WhereExpression = 'WHERE ' ComparisonExpression (' AND '|' OR ') LogicExpression | ComparisonExpression
 *
 * @example WHERE `abc` = 30
 * @example WHERE `abc` = 30 AND (`foo` = 30 OR `foo` = 40)
 */
class WhereExpression extends LogicalExpression
{
    public function __toString()
    {
        $sql = '';
        foreach ($this->expressions as $index => $expression) {
            if ($index) {
                $sql .= ' ' . $this->operators[$index] . ' ';
            }
            $sql .= $expression->__toString();
        }
        return $sql ? 'WHERE '. $sql : '';
    }
}
