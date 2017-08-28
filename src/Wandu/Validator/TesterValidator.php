<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\Tester;
use Wandu\Validator\Contracts\Validator;
use Wandu\Validator\Exception\InvalidValueException;

class TesterValidator implements Validator
{
    /** @var string */
    protected $name;
    
    /** @var \Wandu\Validator\Contracts\Tester */
    protected $tester;
    
    public function __construct(string $name, Tester $tester)
    {
        $this->name = $name;
        $this->tester = $tester;
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function assert($data)
    {
        if (!$this->tester->test($data)) {
            throw new InvalidValueException([$this->name]);
        }
    }

    /**
     * @param mixed $data
     * @return bool
     */
    public function validate($data): bool
    {
        return $this->tester->test($data);
    }
}
