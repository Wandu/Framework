<?php
namespace Wandu\Validator;

use Exception;
use Throwable;
use Wandu\Validator\Contracts\ErrorThrowable;
use Wandu\Validator\Contracts\Validatable;
use Wandu\Validator\Exception\InvalidValueException;
use Wandu\Validator\Throwable\ErrorBag;
use Wandu\Validator\Throwable\ErrorThrower;

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
        $dataKeys = array_flip(is_array($data) ? array_keys($data) : []);
        foreach ($this->normalizer->normalize($rule) as $target => $nextRule) {
            if (!$target) {
                foreach ($nextRule as $condition) {
                    if (!$this->loader->load($condition)->test($data, $origin)) {
                        $thrower->throws($condition, $keys);
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

                array_push($keys, $name);
                if (!isset($data[$name])) {
                    $thrower->throws("required", $keys);
                } elseif (count($target->getIterator()) && !is_array($data[$name])) {
                    $thrower->throws("array", $keys);
                } else {
                    $this->checkIterator($target->getIterator(), $nextRule, $thrower, $data[$name], $origin, $keys);
                }
                array_pop($keys);
            }
        }
        foreach ($dataKeys as $dataKey => $_) {
            $thrower->throws('unknown', array_merge($keys, [$dataKey]));
        }
    }
    
    protected function checkIterator(array $iterators, $rule, ErrorThrowable $thrower, $data, $origin, array $keys = [])
    {
        if (count($iterators)) {
            $iterator = array_shift($iterators);
            if ($iterator !== null && $iterator < count($data)) {
                $thrower->throws("array_length:{$iterator}", $keys);
                return;
            }
            foreach ($data as $index => $value) {
                array_push($keys, $index);
                $this->checkIterator($iterators, $rule, $thrower, $value, $origin, $keys);
                array_pop($keys);
            }
        } else {
            $this->check($rule, $thrower, $data, $origin, $keys);
        }
    }
}
