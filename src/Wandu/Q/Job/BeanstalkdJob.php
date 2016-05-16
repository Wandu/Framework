<?php
namespace Wandu\Q\Job;

use Pheanstalk\Job;
use Pheanstalk\PheanstalkInterface;
use Wandu\Q\Contracts\JobInterface;
use Wandu\Q\Contracts\SerializerInterface;

class BeanstalkdJob implements JobInterface
{
    /** @var \Pheanstalk\PheanstalkInterface */
    protected $client;

    /** @var \Pheanstalk\Job */
    protected $job;
    
    /** @var \Wandu\Q\Contracts\SerializerInterface */
    protected $serializer;
    
    public function __construct(
        PheanstalkInterface $client,
        Job $job,
        SerializerInterface $serializer
    ) {
        $this->client = $client;
        $this->job = $job;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return $this->serializer->unserialize($this->job->getData());
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        return $this->client->delete($this->job);
    }
}
