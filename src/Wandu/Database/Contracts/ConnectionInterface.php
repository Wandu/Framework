<?php
namespace Wandu\Database\Contracts;

use Wandu\Database\QueryBuilder;

interface ConnectionInterface
{
    /**
     * @param string $tableName
     * @return \Wandu\Database\QueryBuilder
     */
    public function createQueryBuilder(string $tableName): QueryBuilder;
    
    /**
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function connect(): ConnectionInterface;

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \Traversable
     */
    public function fetch($query, array $bindings = []);

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return array
     */
    public function first($query, array $bindings = []);
    
    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return int
     */
    public function query($query, array $bindings = []);

    /**
     * @return string|int
     */
    public function getLastInsertId();
    
    /**
     * @param callable $handler
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function transaction(callable $handler);
}
