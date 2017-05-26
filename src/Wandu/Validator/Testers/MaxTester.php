<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\TesterInterface;

class MaxTester implements TesterInterface
{
    /** @var int */
    protected $max;

    /**
     * @param int $max
     */
    public function __construct($max)
    {
        $this->max = $max;
    }
    
    /**
     * {@inheritdoc}
     */
    public function test($data): bool
    {
        if ($data === null) return false;
        return $data <= $this->max;
    }
}
