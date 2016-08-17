<?php
namespace Wandu\Database\Connection;

use PDO;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Query\QueryBuilder;

class MysqlConnection implements ConnectionInterface
{
    /** @var \PDO */
    protected $pdo;

    /** @var string */
    protected $tablePrefix;

    /**
     * @param \PDO $pdo
     * @param string $tablePrefix
     */
    public function __construct(PDO $pdo, $tablePrefix = '')
    {
        $this->pdo = $pdo;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * @param string $table
     * @return \Wandu\Database\Query\QueryBuilder
     */
    public function createQueryBuilder($table)
    {
        return new QueryBuilder($this->tablePrefix . $table);
    }
}
