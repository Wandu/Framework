<?php
namespace Wandu\Q\Contracts;

interface Adapter
{
    /**
     * @return void
     */ 
    public function flush();
    
    /**
     * @param string $payload
     * @return void
     */
    public function send(string $payload);

    /**
     * @return \Wandu\Q\Contracts\AdapterJob
     */
    public function receive();

    /**
     * @param \Wandu\Q\Contracts\AdapterJob $job
     * @return void
     */
    public function remove($job);
}
