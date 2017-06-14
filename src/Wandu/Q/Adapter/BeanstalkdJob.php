<?php
namespace Wandu\Q\Adapter;

use Pheanstalk\Job as PheanstalkJob;
use Wandu\Q\Contracts\AdapterJob;

class BeanstalkdJob implements AdapterJob
{
    /** @var \Pheanstalk\Job */
    protected $job;
    
    public function __construct(PheanstalkJob $job)
    {
        $this->job = $job;
    }

    /**
     * @return \Pheanstalk\Job
     */
    public function getJob(): PheanstalkJob
    {
        return $this->job;
    }

    /**
     * {@inheritdoc}
     */
    public function payload(): string
    {
        return $this->job->getData();
    }
}
