<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\Rule;
use Wandu\Validator\Contracts\Validatable;
use Wandu\Validator\Exception\InvalidValueException;

class Validator implements Validatable
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $loader;

    /** @var array */
    protected $rule;

    public function __construct(TesterLoader $loader, $rule)
    {
        $this->loader = $loader;
        $this->rule = $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function assert($data)
    {
        $this->check($this->rule, $errors = new ErrorBag(), $data, $data);
        if (count($errors)) {
            throw new InvalidValueException($errors->errors());
        }
    }

    /**
     * @param mixed $data
     * @return bool
     */
    public function validate($data): bool
    {
//        return $this->tester->test($data);
    }

    public function check($rule, ErrorBag $errors, $data, $origin)
    {
        /**
         * @var \Wandu\Validator\TargetName $target
         */
        foreach ($this->normalizeRule($rule) as list($target, $condition)) {
            if (!$target) {
                if (!$this->loader->load($condition)->test($data, $origin)) {
                    $errors->store($condition);
                }
                continue;
            }
            $name = $target->getName();

            // check optional
            if ($target->isOptional() && !isset($data[$name])) {
                continue;
            }
            if (!isset($data[$name])) {
                $errors->store("required", [$name]);
                continue;
            }
            if (count($target->getIterator()) && !is_array($data[$name])) {
                $errors->store("array", [$name]);
                continue;
            }

            $errors->pushPrefix($name);
            $this->checkIterator($target->getIterator(), $condition, $errors, $data[$name], $origin);
            $errors->popPrefix();
        }
    }
    
    protected function checkIterator($iterators, $rule, ErrorBag $errors, $data, $origin)
    {
        if (count($iterators)) {
            $iterator = array_shift($iterators);
            if ($iterator !== null && $iterator < count($data)) {
                $errors->store("array_length:{$iterator}");
                return;
            }
            foreach ($data as $index => $value) {
                $errors->pushPrefix($index);
                $this->checkIterator($iterators, $rule, $errors, $value, $origin);
                $errors->popPrefix();
            }
        } else {
            $this->check($rule, $errors, $data, $origin);
        }
    }

    /**
     * @param string|array|callable|\Wandu\Validator\Contracts\Rule $rule
     * @return \Generator
     */
    protected function normalizeRule($rule)
    {
        while (is_callable($rule) || (is_object($rule) && $rule instanceof Rule)) {
            if (is_callable($rule)) {
                $rule = call_user_func($rule);
            } elseif ($rule instanceof Rule) {
                $rule = $rule->definition();
            }
        }
        // string -> array
        if (!is_array($rule)) {
            $rule = [$rule];
        }
        foreach ($rule as $ruleTarget => $ruleCondition) {
            yield [
                !is_int($ruleTarget) ? TargetName::parse($ruleTarget) : null,
                $ruleCondition
            ];
        }
    }
}
