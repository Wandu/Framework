<?php
namespace Wandu\Validator;

use Closure;
use Wandu\Validator\Contracts\Rule;
use Wandu\Validator\Contracts\RuleDefinition;

class AssertRuleDefinition implements RuleDefinition
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $tester;
    
    /** @var \Wandu\Validator\ErrorBag */
    protected $errors;
    
    /** @var mixed $data */
    protected $data;
    
    /** @var mixed $origin */
    protected $origin;
    
    public function __construct(TesterLoader $tester, ErrorBag $errors, $data, $origin)
    {
        $this->tester = $tester;
        $this->errors = $errors;
        $this->data = $data;
        $this->origin = $origin;
    }

    /**
     * {@inheritdoc}
     */
    public function prop(string $target, ...$rules)
    {
        $targetName = $target;
        $iterable = 0;
        $optional = false;

        if (strpos($targetName, '?') !== false) {
            $targetName = trim($targetName, '?');
            $optional = true;
        }
        while (strpos($targetName, "[]") !== false) {
            $targetName = str_replace("[]", "", $targetName);
            $iterable++;
        }

        if ($optional && !isset($this->data[$targetName])) return;
        if (!isset($this->data[$targetName])) {
            $this->errors->store("required", [$targetName]);
            return;
        }

        if ($iterable && !is_array($this->data[$targetName])) {
            $this->errors->store("array", [$targetName]);
            return;
        }

        $this->errors->pushPrefix($targetName);
        if ($iterable) {
            foreach ($this->data[$targetName] as $index => $subData) {
                $this->errors->pushPrefix($index);
                $this->checkRules($rules, $subData, $this->origin);
                $this->errors->popPrefix();
            }
        } else {
            $this->checkRules($rules, $this->data[$targetName], $this->origin);
        }
        $this->errors->popPrefix();
    }

    /**
     * @param string[]|\Wandu\Validator\Contracts\Rule[]|\Closure[] $rules
     * @param mixed $data
     * @param mixed $origin
     */
    protected function checkRules(array $rules, $data, $origin)
    {
        foreach ($rules as $rule) {
            if ($rule instanceof Rule) {
                $rule->define(new AssertRuleDefinition($this->tester, $this->errors, $data, $origin));
            } elseif ($rule instanceof Closure) {
                $rule->__invoke(new AssertRuleDefinition($this->tester, $this->errors, $data, $origin));
            } else {
                if (!$this->tester->load($rule)->test($data, $origin)) {
                    $this->errors->store($rule);
                }
            }
        }
    }
}
