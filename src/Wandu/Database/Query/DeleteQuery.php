<?php
namespace Wandu\Database\Query;

use Wandu\Database\Contracts\QueryInterface;
use Wandu\Database\Query\Expression\HasWhereExpression;
use Wandu\Database\Support\Helper;

class DeleteQuery extends HasWhereExpression implements QueryInterface
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
        $parts = ['DELETE FROM ' . Helper::normalizeName($this->table)];
        if ($part = parent::toSql()) {
            $parts[] = $part;
        }
        return implode(' ', $parts);
    }
}
