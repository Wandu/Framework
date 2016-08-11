<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\InvalidValueException;

class OptionalValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'optional';

    /** @var \Wandu\Validator\Contracts\ValidatorInterface */
    protected $next;
    
    /**
     * @param \Wandu\Validator\Contracts\ValidatorInterface $next
     */
    public function __construct(ValidatorInterface $next = null)
    {
        $this->next = $next;
    }

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return $item === null || $item === '';
    }

    /**
     * {@inheritdoc}
     */
    public function assert($item)
    {
        if ($this->test($item)) {
            return;
        }
        if (isset($this->next)) {
            $this->next->assert($item);
            return;
        } else {
            throw $this->createException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        if ($this->test($item)) {
            return true;
        }
        if (isset($this->next)) {
            return $this->next->validate($item);
        }
        return false;
    }
}
