<?php
namespace Wandu\Database\Query;

use Wandu\Database\Contracts\ExpressionInterface;
use Wandu\Database\Support\Attributes;

/**
 * @see http://dev.mysql.com/doc/refman/5.7/en/rename-table.html
 *
 * RENAME TABLE tbl_name TO new_tbl_name
 */
class RenameQuery implements ExpressionInterface
{
    use Attributes;

    /** @var string */
    protected $oldTableName;

    /** @var string */
    protected $newTableName;

    /**
     * @param string $oldTableName
     * @param string $newTableName
     */
    public function __construct($oldTableName, $newTableName)
    {
        $this->oldTableName = $oldTableName;
        $this->newTableName = $newTableName;
    }

    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        return "RENAME TABLE `{$this->oldTableName}` TO `{$this->newTableName}`";
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return [];
    }
}
