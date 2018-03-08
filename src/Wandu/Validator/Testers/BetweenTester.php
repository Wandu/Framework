<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

class BetweenTester implements Tester
{
    /** @var int|string */
    protected $min;
    
    /** @var int|string */
    protected $max;

    /**
     * @param int|string $min
     * @param int|string $max
     */
    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        if ($data === null) return false;
        if (is_int($data) || is_float($data)) {
            return $data <= (int) $this->max && $data >= (int) $this->min;
        }
        if (is_string($data)) {
            return strcmp($data, $this->min) >= 0 && strcmp($data, $this->max) <= 0;
        }
        return $data <= $this->max && $data >= $this->min;
    }
}
