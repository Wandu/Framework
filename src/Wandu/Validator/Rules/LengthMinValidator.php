<?php
namespace Wandu\Validator\Rules;

class LengthMinValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'length_min';
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
        return mb_strlen($item, 'utf-8') >= $this->min;
    }
}
