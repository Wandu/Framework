<?php
namespace Wandu\Validator\Rules;

class LengthMinValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'length_min';
    const ERROR_MESSAGE = 'it must be greater or equal than {{min}}';

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
    public function validate($item)
    {
        return mb_strlen($item, 'utf-8') >= $this->min;
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorMessage()
    {
        return str_replace('{{min}}', $this->min, static::ERROR_MESSAGE);
    }
}
