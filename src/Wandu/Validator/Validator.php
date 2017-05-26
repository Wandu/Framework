<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\RuleInterface;
use Wandu\Validator\Exception\InvalidValueException;

class Validator
{
    /**
     * @param string|\Wandu\Validator\Contracts\RuleInterface|\Wandu\Validator\Contracts\TesterInterface $rule
     * @return \Wandu\Validator\Validator
     */
    public static function create($rule): Validator
    {
        if ($rule instanceof RuleInterface) return new Validator($rule);
        if (is_string($rule)) {
            $rule = tester($rule);
        }
        // all rule is TesterInterface
        
    }
    
    /** @var \Wandu\Validator\Contracts\RuleInterface */
    protected $rule;
    
    /**
     * @param \Wandu\Validator\Contracts\RuleInterface $rule
     */
    public function __construct(RuleInterface $rule)
    {
        $this->rule = $rule;
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function assert($data)
    {
        $errorBag = new ErrorBag();
        $this->rule->define(new AssertRuleDefinition($errorBag, $data));
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
