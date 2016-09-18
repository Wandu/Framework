<?php
namespace Wandu\Database\Schema\Expression;

use Wandu\Database\Query\ExpressionInterface;
use Wandu\Database\Support\Attributes;

/**
 * @see http://dev.mysql.com/doc/refman/5.7/en/drop-table.html
 *
 * DROP TABLE [IF EXISTS] tbl_name [RESTRICT | CASCADE]
 *
 * @method \Wandu\Database\Schema\Expression\DropExpression ifExists()
 * @method \Wandu\Database\Schema\Expression\DropExpression restrict()
 * @method \Wandu\Database\Schema\Expression\DropExpression cascade()
 */
class DropExpression implements ExpressionInterface
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
        $sql = "DROP TABLE";
        if (isset($this->attributes['if_exists'])) {
            $sql .= ' IF EXISTS';
        }
        $sql .= " `{$this->table}`";
        if (isset($this->attributes['restrict']) && $this->attributes['restrict']) {
            $sql .= ' RESTRICT';
        }
        if (isset($this->attributes['cascade']) && $this->attributes['cascade']) {
            $sql .= ' CASCADE';
        }
        return $sql;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return [];
    }
}
