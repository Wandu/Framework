<?php
namespace Wandu\Validator\Exception;

use RuntimeException;

class InvalidValueException extends RuntimeException
{
    /** @var array */
    protected $messages;

    /**
     * @param string $type
     * @param string $message
     */
    public function __construct($type = null, $message = '')
    {
        parent::__construct('invalid value');
        if ($type) {
            $this->setMessage($type, $message);
        }
    }

    /**
     * @param string $type
     * @param string $message
     */
    public function setMessage($type, $message)
    {
        $this->messages[$type][] = $message;
    }

    /**
     * @param array $messages
     */
    public function setMessages(array $messages)
    {
        foreach ($messages as $key => $message) {
            $this->messages[$key] = array_merge(
                isset($this->messages[$key]) ? $this->messages[$key] : [],
                $message
            );
        }
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
