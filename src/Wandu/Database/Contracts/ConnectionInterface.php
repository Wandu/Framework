<?php
namespace Wandu\Database\Contracts;

interface ConnectionInterface
{
    /**
     * @param array $config
     */
    public function setConfig(array $config = []);
    
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
     * @return bool
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
