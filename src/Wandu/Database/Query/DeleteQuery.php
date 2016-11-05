<?php
namespace Wandu\Database\Query;

use Wandu\Database\Query\Expression\HasWhereExpression;

class DeleteQuery extends HasWhereExpression
{
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
        $parts = ['DELETE FROM `' . $this->table . '`'];
        if ($part = parent::toSql()) {
            $parts[] = $part;
        }
        return implode(' ', $parts);
    }
}
