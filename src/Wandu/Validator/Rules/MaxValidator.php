<?php
namespace Wandu\Validator\Rules;

class MaxValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'max:{{max}}';

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
        return $item <= $this->max;
    }
}
