<?php
namespace Wandu\Database\Connection;

use Exception;
use PDO;
use PDOStatement;
use Throwable;
use Wandu\Database\Contracts\Connection;
use Wandu\Database\Events\ExecuteQuery;
use Wandu\Event\Contracts\EventEmitter;

class MysqlConnection implements Connection
{
    /** @var \PDO */
    protected $pdo;

    /** @var \Wandu\Event\Contracts\EventEmitter */
    protected $emitter;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function setEventEmitter(EventEmitter $emitter)
    {
        $this->emitter = $emitter;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(string $query, array $bindings = [])
    {
        $statement = $this->execute($query, $bindings);
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function first(string $query, array $bindings = [])
    {
        $attributes = $this->execute($query, $bindings)->fetch(PDO::FETCH_ASSOC);
        return $attributes ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $query, array $bindings = [])
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
     * @param string $query
     * @param array $bindings
     * @return \PDOStatement
     */
    protected function execute(string $query, array $bindings = [])
    {
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
