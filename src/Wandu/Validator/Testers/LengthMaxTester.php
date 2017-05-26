<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\TesterInterface;

class LengthMaxTester implements TesterInterface
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
        if (is_array($data)) {
            return count($data) <= $this->max;
        }
        return mb_strlen($data, 'utf-8') <= $this->max;
    }
}
