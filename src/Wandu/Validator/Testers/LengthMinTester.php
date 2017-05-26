<?php
namespace Wandu\Validator\Testers;

use Wandu\Validator\Contracts\TesterInterface;

class LengthMinTester implements TesterInterface
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
    public function test($data): bool
    {
        if (is_array($data)) {
            return count($data) >= $this->min;
        }
        return mb_strlen($data, 'utf-8') >= $this->min;
    }
}
