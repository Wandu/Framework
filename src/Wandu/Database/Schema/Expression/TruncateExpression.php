<?php
namespace Wandu\Database\Schema\Expression;

use Wandu\Database\Query\ExpressionInterface;
use Wandu\Database\Support\Attributes;

/**
 * @see http://dev.mysql.com/doc/refman/5.7/en/truncate-table.html
 *
 * TRUNCATE TABLE tbl_name
 */
class TruncateExpression implements ExpressionInterface
{
    use Attributes;

    /** @var string */
    protected $table;

    /**
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        return "TRUNCATE TABLE `{$this->table}`";
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return [];
    }
}
