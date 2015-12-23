<?php
namespace Wandu\Queue\Job;

use Aws\Sqs\SqsClient;
use Wandu\Queue\Contracts\JobInterface;

class SqsJob implements JobInterface
{
    /** @var \Aws\Sqs\SqsClient */
    protected $client;

    /** @var string */
    protected $url;

    /** @var string */
    protected $body;

    /** @var string */
    protected $handle;

    /**
     * @param \Aws\Sqs\SqsClient $client
     * @param string $url
     * @param string $handle
     * @param string $body
     */
    public function __construct(SqsClient $client, $url, $handle, $body)
    {
        $this->client = $client;
        $this->url = $url;
        $this->handle = $handle;
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        return $this->client->deleteMessage([
            'QueueUrl'  => $this->url,
            'ReceiptHandle' => $this->handle,
        ]);
    }
}
