<?php
namespace Wandu\Database\Connection;

use Exception;
use Throwable;
use PDO;
use PDOStatement;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Query\QueryBuilder;

class MysqlConnection implements ConnectionInterface
{
    /** @var \PDO */
    protected $pdo;

    /** @var string */
    protected $prefix;

    /**
     * @param \PDO $pdo
     * @param string $prefix
     */
    public function __construct(PDO $pdo, $prefix = '')
    {
        $this->pdo = $pdo;
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder($table)
    {
        return new QueryBuilder($this->prefix . $table);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($query, array $bindings = [])
    {
        $statement = $this->prepare($query, $bindings);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function query($query, array $bindings = [])
    {
        $statement = $this->prepare($query, $bindings);
        return $statement->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function transaction(callable $handler)
    {
        $this->pdo->beginTransaction();
        try {
            call_user_func($handler, $this);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
        $this->pdo->commit();
    }

    /**
     * @param string|callable|\Wandu\Database\Query\QueryBuilder $query
     * @param array $bindings
     * @return \PDOStatement
     */
    protected function prepare($query, array $bindings = [])
    {
        if (is_callable($query)) {
            $query = call_user_func($query, $this);
        }
        if ($query instanceof QueryBuilder) {
            $bindings = $query->getBindings();
            $query = $query->toSql();
        }
        $statement = $this->pdo->prepare($query);
        $this->bindValues($statement, $bindings);
        return $statement;
    }

    /**
     * @param \PDOStatement $statement
     * @param array $bindings
     */
    protected function bindValues(PDOStatement $statement, array $bindings = [])
    {
        foreach ($bindings as $key => $value) {
            if (is_int($value)) {
                $dataType = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $dataType = PDO::PARAM_BOOL;
            } elseif (is_null($value)) {
                $dataType = PDO::PARAM_NULL;
            } else {
                $dataType = PDO::PARAM_STR;
            }
            $statement->bindValue(
                is_int($key) ? $key + 1 : $key,
                $value,
                $dataType
            );
        }
    }
}
