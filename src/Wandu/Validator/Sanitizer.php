<?php
namespace Wandu\Validator;

use ReflectionClass;

class Sanitizer
{
    /** @var \Wandu\Validator\Validator */
    protected $validator;
    
    /** @var string */
    protected $T;
    
    /** @var \ReflectionClass */
    private $refl;

    /**
     * @param string|\Wandu\Validator\Contracts\RuleInterface|\Wandu\Validator\Contracts\TesterInterface|\Wandu\Validator\Validator $rule
     * @param string $T
     */
    public function __construct($rule, string $T = null)
    {
        if ($rule instanceof Validator) {
            $this->validator = $rule;
        } else {
            $this->validator = validator($rule);
        }
        $this->T = $T;
    }

    /**
     * @param mixed $data
     * @return mixed|{$this->T}
     */
    public function sanitize($data)
    {
        $this->validator->assert($data);
        if (!$this->T || !class_exists($this->T)) return $data;
        $refl = $this->getReflectionClass();
        $instance = $refl->newInstanceWithoutConstructor();
        foreach ($refl->getProperties() as $reflectionProperty) {
            $propertyName = $reflectionProperty->getName();
            if (isset($data[$propertyName])) {
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($instance, $data[$propertyName]);
            }
        }
        return $instance;
    }

    /**
     * @return \ReflectionClass
     */
    protected function getReflectionClass(): ReflectionClass
    {
        if (!isset($this->refl)) {
            $this->refl = new ReflectionClass($this->T);
        }
        return $this->refl;
    }
}
