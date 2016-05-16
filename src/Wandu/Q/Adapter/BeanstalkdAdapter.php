<?php
namespace Wandu\Q\Adapter;

use Pheanstalk\PheanstalkInterface;
use Wandu\Q\Contracts\AdapterInterface;
use Wandu\Q\Contracts\SerializerInterface;
use Wandu\Q\Job\BeanstalkdJob;

class BeanstalkdAdapter implements AdapterInterface
{
    /** @var \Pheanstalk\PheanstalkInterface */
    protected $client;

    /**
     * @param \Pheanstalk\PheanstalkInterface $client
     */
    public function __construct(PheanstalkInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue(SerializerInterface $serializer, $payload)
    {
        $this->client->put($serializer->serialize($payload));
    }

    /**
     * {@inheritdoc}
     */
    public function dequeue(SerializerInterface $serializer)
    {
        $job = $this->client->reserve();
        if ($job) {
            return new BeanstalkdJob($this->client, $job, $serializer);
        }
        return null;
    }
}
