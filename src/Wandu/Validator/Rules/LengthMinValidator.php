<?php
namespace Wandu\Validator\Rules;

class LengthMinValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'length_min:{{min}}';

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
        if (is_array($item)) {
            return count($item) >= $this->min;
        }
        return mb_strlen($item, 'utf-8') >= $this->min;
    }
}
