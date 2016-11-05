<?php
namespace Wandu\Database\Query;

use Wandu\Database\Contracts\QueryInterface;
use Wandu\Database\Support\Attributes;

/**
 * @see http://dev.mysql.com/doc/refman/5.7/en/drop-table.html
 *
 * DROP TABLE [IF EXISTS] tbl_name [RESTRICT | CASCADE]
 *
 * @method \Wandu\Database\Query\DropQuery ifExists()
 * @method \Wandu\Database\Query\DropQuery restrict()
 * @method \Wandu\Database\Query\DropQuery cascade()
 */
class DropQuery implements QueryInterface
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
