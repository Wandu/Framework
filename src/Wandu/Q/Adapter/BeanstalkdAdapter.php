<?php
namespace Wandu\Q\Adapter;

use Pheanstalk\Exception\ServerException;
use Pheanstalk\PheanstalkInterface;
use Wandu\Q\Contracts\Adapter;

class BeanstalkdAdapter implements Adapter
{
    /** @var \Pheanstalk\PheanstalkInterface */
    protected $client;
    
    public function __construct(PheanstalkInterface $client, string $channel = "default")
    {
        $this->client = $client;
        $this->client->useTube($channel);
        $this->client->watch($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        try {
            while ($this->client->delete($this->client->peekDelayed())) {}
        } catch (ServerException $e) {
        }
        try {
            while ($this->client->delete($this->client->peekReady())) {}
        } catch (ServerException $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $payload)
    {
        $this->client->put($payload);
    }

    /**
     * {@inheritdoc}
     */
    public function receive()
    {
        try {
            return new BeanstalkdJob($this->client->peekReady());
        } catch (ServerException $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($job)
    {
        /** @var \Wandu\Q\Adapter\BeanstalkdJob $job */
        return $this->client->delete($job->getJob());
    }
}
