<?php
namespace Wandu\Q\Adapter;

use Pheanstalk\Exception\ServerException;
use Pheanstalk\PheanstalkInterface;
use Wandu\Q\Contracts\Adapter;

class BeanstalkdAdapter implements Adapter
{
    /** @var \Pheanstalk\PheanstalkInterface */
    protected $client;
    
    /** @var string */
    protected $channel;
    
    public function __construct(PheanstalkInterface $client, string $channel = "default")
    {
        $this->client = $client;
        $this->channel = $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $client = $this->client->useTube($this->channel);
        try {
            while ($client->delete($client->peekDelayed())) {}
        } catch (ServerException $e) {
        }
        try {
            while ($client->delete($client->peekReady())) {}
        } catch (ServerException $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $payload)
    {
        $this->client->useTube($this->channel)->put($payload);
    }

    /**
     * {@inheritdoc}
     */
    public function receive()
    {
        try {
            return new BeanstalkdJob($this->client->watch($this->channel)->peekReady());
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
