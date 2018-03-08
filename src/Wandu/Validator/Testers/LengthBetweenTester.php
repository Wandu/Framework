<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\Tester;

class LengthBetweenTester implements Tester
{
    /** @var int */
    protected $min;

    /** @var int */
    protected $max;
    
    /**
     * @param int $min
     * @param int $max
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
        if (is_array($data) || $data instanceof \Countable) {
            $length = count($data);
        } elseif (is_string($data)) {
            $length = mb_strlen($data, 'utf-8');
        }
        return $length >= $this->min && $length <= $this->max;
    }
}
