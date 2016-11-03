<?php
namespace Wandu\Validator\Rules;

class RequiredValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'required';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return isset($item) && $item !== '';
    }

    /**
     * {@inheritdoc}
     */
    public function assert($item)
    {
        if (!$this->test($item)) {
            throw $this->createException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        return $this->test($item);
    }
}
