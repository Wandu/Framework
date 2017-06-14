<?php
namespace Wandu\Q\Adapter;

use Wandu\Q\Contracts\AdapterJob;

class ArrayJob implements AdapterJob
{
    /** @var string */
    protected $identifier;

    /** @var string */
    protected $payload;

    public function __construct($identifier, $payload)
    {
        $this->identifier = $identifier;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function payload(): string
    {
        return $this->payload;
    }
}
