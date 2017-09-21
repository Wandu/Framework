<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\Validatable;
use Wandu\Validator\Exception\InvalidValueException;

class Validator implements Validatable
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $loader;

    /** @var \Wandu\Validator\RuleNormalizer */
    protected $normalizer;
    
    /** @var array */
    protected $rule;

    public function __construct(TesterLoader $loader, RuleNormalizer $normalizer, $rule)
    {
        $this->loader = $loader;
        $this->normalizer = $normalizer;
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
        $dataKeys = array_flip(is_array($data) ? array_keys($data) : []);
        foreach ($this->normalizer->normalize($rule) as $target => $nextRule) {
            if (!$target) {
                foreach ($nextRule as $condition) {
                    if (!$this->loader->load($condition)->test($data, $origin)) {
                        $errors->store($condition);
                    }
                }
            } else {
                $target = TargetName::parse($target);
                $name = $target->getName();
                unset($dataKeys[$name]); // remove
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
                $this->checkIterator($target->getIterator(), $nextRule, $errors, $data[$name], $origin);
                $errors->popPrefix();
            }
        }
        foreach ($dataKeys as $dataKey => $_) {
            $errors->store('unknown', [$dataKey]);
        }
    }
    
    protected function checkIterator(array $iterators, $rule, ErrorBag $errors, $data, $origin)
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
}
