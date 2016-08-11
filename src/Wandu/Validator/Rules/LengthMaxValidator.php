<?php
namespace Wandu\Validator\Rules;

class LengthMaxValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'length_max:{{max}}';

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
        if (is_array($item)) {
            return count($item) <= $this->max;
        }
        return mb_strlen($item, 'utf-8') <= $this->max;
    }
}
