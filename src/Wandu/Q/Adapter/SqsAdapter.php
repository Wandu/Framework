<?php
namespace Wandu\Q\Adapter;

use Aws\Sqs\Exception\SqsException;
use Aws\Sqs\SqsClient;
use Wandu\Q\Contracts\Adapter;

class SqsAdapter implements Adapter
{
    /** @var \Aws\Sqs\SqsClient */
    protected $client;

    /** @var string */
    protected $url;
    
    public function __construct(SqsClient $client, $url)
    {
        $this->client = $client;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        try {
            $this->client->purgeQueue([
                'QueueUrl' => $this->url,
            ]);
        } catch (SqsException $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $payload)
    {
        $this->client->sendMessage([
            'QueueUrl' => $this->url,
            'MessageBody' => $payload,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function receive()
    {
        $receiveResult = $this->client->receiveMessage([
            'QueueUrl' => $this->url,
            'MaxNumberOfMessages' => 1,
        ]);
        print_r($receiveResult);
        if ($receiveResult->search("Messages") && ((int)$receiveResult->search("Messages | length(@)")) > 0) {
            return new SqsJob(
                $receiveResult->search("Messages[0].ReceiptHandle"),
                $receiveResult->search("Messages[0].Body")
            );
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($job)
    {
        /** @var \Wandu\Q\Adapter\SqsJob $job */
        $this->client->deleteMessage([
            'QueueUrl'  => $this->url,
            'ReceiptHandle' => $job->getReceiptHandler(),
        ]);
    }
}
