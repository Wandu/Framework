<?php
namespace Wandu\Q\Adapter;

use Wandu\Q\Contracts\Adapter;

class NullAdapter implements Adapter
{
    /**
     * {@inheritdoc}
     */
    public function send(string $payload)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function receive()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($job)
    {
    }
}
