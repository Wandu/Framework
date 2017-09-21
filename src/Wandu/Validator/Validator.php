<?php
namespace Wandu\Validator;

use Exception;
use Throwable;
use Wandu\Validator\Contracts\ErrorThrowable;
use Wandu\Validator\Contracts\RuleNormalizable;
use Wandu\Validator\Contracts\Validatable;
use Wandu\Validator\Exception\InvalidValueException;
use Wandu\Validator\Throwable\ErrorBag;
use Wandu\Validator\Throwable\ErrorThrower;

class Validator implements Validatable
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $loader;

    /** @var \Wandu\Validator\Contracts\RuleNormalizable */
    protected $normalizer;
    
    /** @var array */
    protected $rule;

    public function __construct(TesterLoader $loader, RuleNormalizable $normalizer, $rule)
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
        $errorBag = new ErrorBag();
        $this->check($this->rule, $errorBag, $data, $data);
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
            $this->check($this->rule, new ErrorThrower(), $data, $data);
            return true;
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }
        return false;
    }

    public function check($rule, ErrorThrowable $thrower, $data, $origin, $keys = [])
    {
        $this->checkSingle($this->normalizer->normalize($rule), $thrower, $data, $origin, $keys);
    }

    public function checkSingle($normalizedRule, ErrorThrowable $thrower, $data, $origin, $keys = [])
    {
        $dataKeys = array_flip(is_array($data) ? array_keys($data) : []);
        list($conditions, $attributes) = $normalizedRule;
        foreach ($conditions as $condition) {
            if (!$this->loader->load($condition)->test($data, $origin)) {
                $thrower->throws($condition, $keys);
            }
        }
        foreach ($attributes as list(list($name, $iterator, $optional), $children)) {
            unset($dataKeys[$name]); // remove
            // check optional
            if ($optional && !isset($data[$name])) {
                continue;
            }
            array_push($keys, $name);
            if (!isset($data[$name])) {
                $thrower->throws("required", $keys);
            } elseif (count($iterator) && !is_array($data[$name])) {
                $thrower->throws("array", $keys);
            } else {
                $this->checkChildren($iterator, $children, $thrower, $data[$name], $origin, $keys);
            }
            array_pop($keys);
        }
        foreach ($dataKeys as $dataKey => $_) {
            $thrower->throws('unknown', array_merge($keys, [$dataKey]));
        }
    }
    
    protected function checkChildren(array $iterators, $rule, ErrorThrowable $thrower, $data, $origin, array $keys = [])
    {
        if (count($iterators)) {
            $iterator = array_shift($iterators);
            if ($iterator !== null && $iterator < count($data)) {
                $thrower->throws("array_length:{$iterator}", $keys);
                return;
            }
            foreach ($data as $index => $value) {
                array_push($keys, $index);
                $this->checkChildren($iterators, $rule, $thrower, $value, $origin, $keys);
                array_pop($keys);
            }
        } else {
            $this->checkSingle($rule, $thrower, $data, $origin, $keys);
        }
    }
}
