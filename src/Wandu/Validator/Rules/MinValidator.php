<?php
namespace Wandu\Validator\Rules;

class MinValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'min';
    const ERROR_MESSAGE = '{{name}} must be greater or equal than {{min}}';

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
