<?php
namespace Wandu\Validator\Rules;

class LengthMaxValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'length_max';
    const ERROR_MESSAGE = '{{name}} must be less or equal than {{max}}';

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
    public function test($item)
    {
        return mb_strlen($item, 'utf-8') <= $this->max;
    }
}
