<?php
namespace Wandu\Database\Contracts;

interface ConnectionInterface
{
    /**
     * @return string
     */
    public function getPrefix();

    /**
     * @param string $table
     * @return \Wandu\Database\QueryBuilder
     */
    public function createQueryBuilder($table);

    /**
     * @param string $className
     * @return \Wandu\Database\Contracts\RepositoryInterface
     */
    public function createRepository($className);

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \Generator
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
     * @throws \Exception
     * @throws \Throwable
     */
    public function transaction(callable $handler);
}
