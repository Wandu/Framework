<?php

namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

class FileSizeTester implements Tester
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
    public function test($data, $origin = null, array $keys = []): bool
    {
        return (  $this->max > $data );
    }
}

