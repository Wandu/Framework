<?php
namespace Wandu\Q\Adapter;

use Wandu\Q\Contracts\Adapter;
use Countable;

class ArrayAdapter implements Adapter, Countable
{
    /** @var array */
    protected $queue;

    public function __construct()
    {
        $this->queue = [];
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->queue = [];
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->queue);
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $payload)
    {
        $this->queue[] = [
            'id' => uniqid(),
            'payload' => $payload,
            'reserved' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function receive()
    {
        foreach ($this->queue as $idx => $item) {
            if (!$item['reserved']) {
                $this->queue[$idx]['reserved'] = true;
                return new ArrayJob($item['id'], $item['payload']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($job)
    {
        foreach ($this->queue as $idx => $item) {
            /** @var \Wandu\Q\Adapter\ArrayJob $job */
            if ($item['id'] === $job->getIdentifier()) {
                array_splice($this->queue, $idx, 1);
                return;
            }
        }
    }
}
