<?php
namespace Wandu\Validator\Rules;

class MinValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'min:{{min}}';

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
    public function test($item)
    {
        return $item >= $this->min;
    }
}
