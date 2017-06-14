<?php
namespace Wandu\Q\Adapter;

use Wandu\Q\Contracts\AdapterJob;

class SqsJob implements AdapterJob
{
    /** @var string */
    protected $receiptHandler;
    
    /** @var string */
    protected $body;

    public function __construct($receiptHandler, $body)
    {
        $this->receiptHandler = $receiptHandler;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getReceiptHandler()
    {
        return $this->receiptHandler;
    }
    
    /**
     * {@inheritdoc}
     */
    public function payload(): string
    {
        return $this->body;
    }
}
