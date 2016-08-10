<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\InvalidValueException;

class NotValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'not';
    const ERROR_MESSAGE = '';

    /** @var \Wandu\Validator\Contracts\ValidatorInterface */
    protected $next;

    /**
     * @param \Wandu\Validator\Contracts\ValidatorInterface $next
     */
    public function __construct(ValidatorInterface $next)
    {
        $this->next = $next;
    }

    /**
     * {@inheritdoc}
     */
    function test($item)
    {
        // nothing
    }

    /**
     * {@inheritdoc}
     */
    public function assert($item)
    {
        try {
            $this->next->assert($item);
        } catch (InvalidValueException $exception) {
            return;
        }
        $errorType = ValidatorAbstract::ERROR_TYPE;
        $errorMessage = ValidatorAbstract::ERROR_NOT_MESSAGE;
        if ($this->next instanceof ValidatorAbstract) {
            $errorType = $this->next->getErrorType();
            $errorMessage = $this->next->getErrorNotMessage();
        }
        throw new InvalidValueException('not.' . $errorType, $errorMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        return !$this->next->validate($item);
    }
}
