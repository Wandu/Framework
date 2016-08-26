<?php
namespace Wandu\Validator\Exception;

use RuntimeException;

class InvalidValueException extends RuntimeException
{
    /** @var array */
    protected $types = [];

    /**
     * @param \Wandu\Validator\Exception\InvalidValueException[] $exceptions
     * @return \Wandu\Validator\Exception\InvalidValueException
     */
    public static function merge(array $exceptions = [])
    {
        $baseException = new InvalidValueException();
        foreach ($exceptions as $name => $exception) {
            if ($name === '.') {
                $baseException->appendTypes($exception->getTypes());
            } else {
                foreach ($exception->getTypes() as $type) {
                    if (strpos($type, '@') === false) {
                        // ex. "exists" => "exists@thisname"
                        $baseException->appendType("{$type}@{$name}");
                    } else {
                        // ex. "exists@foo" => "exists@thisname.foo"
                        list($type, $key) = explode('@', $type);
                        $baseException->appendType("{$type}@{$name}.{$key}");
                    }
                }
            }
        }
        return $baseException;
    }
    
    /**
     * @param string $type
     */
    public function __construct($type = null)
    {
        parent::__construct('invalid value.');
        if ($type) {
            $this->appendType($type);
        }
    }

    /**
     * @param string $type
     */
    public function appendType($type)
    {
        $this->types[] = $type;
    }

    /**
     * @param array $types
     */
    public function appendTypes(array $types = [])
    {
        $this->types = array_merge($this->types, $types);
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }
}
