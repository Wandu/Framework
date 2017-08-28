<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\Rule;
use Wandu\Validator\Contracts\Validator;
use Wandu\Validator\Exception\InvalidValueException;

class RuleValidator implements Validator
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $tester;

    /** @var \Wandu\Validator\Contracts\Rule */
    protected $rule;

    public function __construct(TesterLoader $tester, Rule $rule)
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
        try {
            $this->assert($data);
            return true;
        } catch (InvalidValueException $e) {}
        return false;
    }
}
