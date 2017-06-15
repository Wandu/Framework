<?php
namespace Wandu\Database\Contracts;

use Wandu\Event\Contracts\EventEmitter;

interface Connection
{
    /**
     * @param \Wandu\Event\Contracts\EventEmitter $emitter
     * @return void
     */
    public function setEventEmitter(EventEmitter $emitter);
    
    /**
     * @param string $query
     * @param array $bindings
     * @return \Traversable
     */
    public function fetch(string $query, array $bindings = []);

    /**
     * @param string $query
     * @param array $bindings
     * @return array
     */
    public function first(string $query, array $bindings = []);
    
    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function query(string $query, array $bindings = []);

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
