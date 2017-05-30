<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

class LengthMinTester implements Tester
{
    /** @var int */
    protected $min;

    /**
     * @param int $min
     */
    public function __construct($min)
    {
        $this->min = $min;
    }

    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        if (is_array($data)) {
            return count($data) >= $this->min;
        }
        return mb_strlen($data, 'utf-8') >= $this->min;
    }
}
