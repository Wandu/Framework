<?php
namespace Wandu\Validator;

use Wandu\Validator\Exception\InvalidValueException;

class Validator
{
    /** @var \Wandu\Validator\TesterFactory */
    protected $tester;
    
    /** @var string|\Wandu\Validator\Contracts\Rule */
    protected $rule;
    
    public function __construct(TesterFactory $tester, $rule)
    {
        $this->tester = $tester;
        $this->rule = $rule;
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function assert($data)
    {
        if (is_string($this->rule)) {
            if (!$this->tester->parse($this->rule)->test($data)) {
                throw new InvalidValueException([$this->rule]);
            }
            return;
        }

        $errorBag = new ErrorBag();
        $this->rule->define(new AssertRuleDefinition($this->tester, $errorBag, $data, $data));
        if (count($errorBag)) {
            throw new InvalidValueException($errorBag->errors());
        }
    }

    /**
     * @param mixed $data
     * @return bool
     */
    public function validate($data): bool
    {
        if (is_string($this->rule)) {
            return $this->tester->parse($this->rule)->test($data);
        }
        try {
            $this->assert($data);
            return true;
        } catch (InvalidValueException $e) {}
        return false;
    }
}
