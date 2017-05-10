<?php
namespace Wandu\Database\Contracts;

use Wandu\Database\Configuration;

interface ConnectionInterface
{
    /**
     * @return \Wandu\Database\Configuration
     */
    public function getConfig(): Configuration;
    
    /**
     * @return $this
     */
    public function connect();

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \Generator
     */
    public function fetch($query, array $bindings = []);

    /**
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     * @param array $bindings
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function all($query, array $bindings = []);

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
