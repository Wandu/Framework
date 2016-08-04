<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use function Wandu\Validator\validator;

class ArrayValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'type.array';
    const ERROR_MESSAGE = 'it must be the array';

    /** @var \Wandu\Validator\Contracts\ValidatorInterface[] */
    protected $attributes = [];
    
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $name => $validator) {
            $this->attributes[$name] = ($validator instanceof ValidatorInterface)
                ? $validator
                : validator()->__call($validator);
        }
    }
    
    public function validate($item)
    {
        if (!is_array($item)) {
            return false;
        }
        foreach ($this->attributes as $name => $validator) {
            if (!$validator->validate($item[$name])) {
                return false;
            }
        }
        return true;
    }
}
