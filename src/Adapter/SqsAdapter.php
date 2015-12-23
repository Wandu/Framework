<?php
namespace Wandu\Q\Adapter;

use Aws\Sqs\SqsClient;
use Wandu\Q\Contracts\AdapterInterface;
use Wandu\Q\Contracts\SerializerInterface;
use Wandu\Q\Job\SqsJob;

class SqsAdapter implements AdapterInterface
{
    /** @var \Aws\Sqs\SqsClient */
    protected $client;

    /** @var string */
    protected $url;

    /**
     * @param string $key
     * @param string $secret
     * @param string $region
     * @param string $url
     */
    public function __construct($key, $secret, $region, $url)
    {
        $this->client = new SqsClient([
            'version' => 'latest',
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
            'region' => $region,
        ]);
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue(SerializerInterface $serializer, $payload)
    {
        $this->client->sendMessage([
            'QueueUrl' => $this->url,
            'MessageBody' => $serializer->serialize($payload),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function dequeue(SerializerInterface $serializer)
    {
        $message = $this->client->receiveMessage([
            'QueueUrl' => $this->url,
        ])->get('Messages');
        if (count($message)) {
            return new SqsJob(
                $this->client,
                $this->url,
                $message[0]['ReceiptHandle'],
                $serializer->unserialize($message[0]['Body'])
            );
        }
        return null;
    }
}
