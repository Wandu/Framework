<?php
namespace Wandu\Q;

use Wandu\Q\Contracts\Adapter;
use Wandu\Q\Contracts\AdapterJob;
use Wandu\Q\Contracts\Serializer;

class Job
{
    /** @var \Wandu\Q\Contracts\Adapter */
    protected $adapter;
    
    /** @var \Wandu\Q\Contracts\Serializer */
    protected $serializer;
    
    /** @var \Wandu\Q\Contracts\AdapterJob */
    protected $job;
    
    public function __construct(Adapter $adapter, Serializer $serializer, AdapterJob $job)
    {
        $this->adapter = $adapter;
        $this->serializer = $serializer;
        $this->job = $job;
    }

    /**
     * @return mixed
     */
    public function read()
    {
        return $this->serializer->unserialize($this->job->payload());
    }

    /**
     * @return void
     */
    public function delete()
    {
        $this->adapter->remove($this->job);
    }
}
