<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\RuleDefnition;
use Wandu\Validator\Contracts\Rule;

class AssertRuleDefinition implements RuleDefnition
{
    /** @var \Wandu\Validator\TesterFactory */
    protected $tester;
    
    /** @var \Wandu\Validator\ErrorBag */
    protected $errors;
    
    /** @var mixed $data */
    protected $data;
    
    public function __construct(TesterFactory $tester, ErrorBag $errors, $data)
    {
        $this->tester = $tester;
        $this->errors = $errors;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function prop(string $target, $rules = null)
    {
        $rules = ($rules ? $rules : []);
        $rules = is_array($rules) ? $rules : [$rules];

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
            $this->errors->throw("required", [$targetName]);
            return;
        }

        if ($iterable && !is_array($this->data[$targetName])) {
            $this->errors->throw("array", [$targetName]);
            return;
        }

        $this->errors->pushPrefix($targetName);
        if ($iterable) {
            foreach ($this->data[$targetName] as $index => $subData) {
                $this->errors->pushPrefix($index);
                foreach ($rules as $rule) {
                    if ($rule instanceof Rule) {
                        $rule->define(new AssertRuleDefinition($this->tester, $this->errors, $subData));
                    } else {
                        if (!$this->tester->parse($rule)->test($subData)) {
                            $this->errors->throw($rule);
                        }
                    }
                }
                $this->errors->popPrefix();
            }
        } else {
            foreach ($rules as $rule) {
                if ($rule instanceof Rule) {
                    $rule->define(new AssertRuleDefinition($this->tester, $this->errors, $this->data[$targetName]));
                } else {
                    if (!$this->tester->parse($rule)->test($this->data[$targetName])) {
                        $this->errors->throw($rule);
                    }
                }
            }
        }
        $this->errors->popPrefix();
    }
}
