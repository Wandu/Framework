<?php
namespace Wandu\Database\Connection;

use Exception;
use PDO;
use PDOStatement;
use Throwable;
use Wandu\Collection\ArrayList;
use Wandu\Database\Configuration;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Contracts\QueryInterface;

class MysqlConnection implements ConnectionInterface
{
    /** @var \PDO */
    protected $pdo;

    /** @var \Wandu\Database\Configuration */
    protected $config;

    /**
     * @param \Wandu\Database\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if (!$this->pdo) {
            $this->pdo = $this->config->createPdo();
        }
        return $this;
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
    public function all($query, array $bindings = [])
    {
        return new ArrayList($this->fetch($query, $bindings));
    }

    /**
     * {@inheritdoc}
     */
    public function first($query, array $bindings = [])
    {
        $statement = $this->prepare($query, $bindings);
        $statement->execute();
        $attributes = $statement->fetch(PDO::FETCH_ASSOC);
        return $attributes ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function query($query, array $bindings = [])
    {
        $statement = $this->prepare($query, $bindings);
        $statement->execute();
        return $statement->rowCount();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
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
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \PDOStatement
     */
    protected function prepare($query, array $bindings = [])
    {
        while (is_callable($query)) {
            $query = call_user_func($query);
        }
        if ($query instanceof QueryInterface) {
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
