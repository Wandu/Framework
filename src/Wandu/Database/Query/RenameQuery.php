<?php
namespace Wandu\Database\Query;

use Wandu\Database\Contracts\QueryInterface;

/**
 * @see http://dev.mysql.com/doc/refman/5.7/en/rename-table.html
 *
 * RENAME TABLE tbl_name TO new_tbl_name
 */
class RenameQuery implements QueryInterface
{
    /** @var string */
    protected $table;

    /** @var string */
    protected $newTable;

    /**
     * @param string $table
     * @param string $newTable
     */
    public function __construct($table, $newTable)
    {
        $this->table = $table;
        $this->newTable = $newTable;
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        return "RENAME TABLE `{$this->table}` TO `{$this->newTable}`";
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return [];
    }
}
