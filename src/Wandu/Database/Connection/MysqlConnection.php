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
use Wandu\Database\Events\Connect;
use Wandu\Database\Events\ExecuteQuery;
use Wandu\Database\QueryBuilder;
use Wandu\Event\Contracts\EventEmitter;

class MysqlConnection implements ConnectionInterface
{
    /** @var \PDO */
    protected $pdo;

    /** @var \Wandu\Database\Configuration */
    protected $config;

    /** @var \Wandu\Event\Contracts\EventEmitter */
    protected $emitter;
    
    public function __construct(
        Configuration $config,
        EventEmitter $emitter = null
    ) {
        $this->config = $config;
        $this->emitter = $emitter;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder(string $tableName): QueryBuilder
    {
        return new QueryBuilder($this->config->getPrefix() . $tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function connect(): ConnectionInterface
    {
        if (!$this->pdo) {
            $this->pdo = $this->config->createPdo();
            if ($this->emitter) {
                $this->emitter->trigger(new Connect());
            }
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($query, array $bindings = [])
    {
        $statement = $this->execute($query, $bindings);
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
        $attributes = $this->execute($query, $bindings)->fetch(PDO::FETCH_ASSOC);
        return $attributes ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function query($query, array $bindings = [])
    {
        return $this->execute($query, $bindings)->rowCount();
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
            $result = call_user_func($handler, $this);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
        $this->pdo->commit();
        return $result;
    }

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \PDOStatement
     */
    protected function execute($query, array $bindings = [])
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
        $statement->execute();
        if ($this->emitter) {
            $this->emitter->trigger(new ExecuteQuery($statement->queryString, $bindings));
        }
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
