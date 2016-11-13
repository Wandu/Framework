<?php
namespace Wandu\Database\Connection;

use Doctrine\Common\Annotations\Reader;
use Exception;
use Interop\Container\ContainerInterface;
use Throwable;
use PDO;
use PDOStatement;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Contracts\QueryInterface;
use Wandu\Database\QueryBuilder;
use Wandu\Database\Repository\Repository;
use Wandu\Database\Repository\RepositorySettings;

class MysqlConnection implements ConnectionInterface
{
    /** @var \PDO */
    protected $pdo;

    /** @var \Interop\Container\ContainerInterface */
    protected $container;

    /** @var string */
    protected $prefix;

    /**
     * @param \PDO $pdo
     * @param \Interop\Container\ContainerInterface $container
     * @param string $prefix
     */
    public function __construct(PDO $pdo, ContainerInterface $container = null, $prefix = '')
    {
        $this->pdo = $pdo;
        $this->container = $container;
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
        return new QueryBuilder($this->getPrefix() . $table);
    }

    /**
     * {@inheritdoc}
     */
    public function createRepository($className)
    {
        if (!$this->container || !$this->container->has(Reader::class)) {
            throw new Exception('cannot create repository!');
        }
        return new Repository($this, RepositorySettings::fromAnnotation($className, $this->container->get(Reader::class)));
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
    public function first($query, array $bindings = [])
    {
        $statement = $this->prepare($query, $bindings);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
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
            $query = call_user_func($query, $this);
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
