<?php
namespace Wandu\Validator\Rules;

class MaxValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'max';
    const ERROR_MESSAGE = 'it must be less or equal than {{max}}';

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
    public function validate($item)
    {
        return $item <= $this->max;
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorMessage()
    {
        return str_replace('{{max}}', $this->max, static::ERROR_MESSAGE);
    }
}
