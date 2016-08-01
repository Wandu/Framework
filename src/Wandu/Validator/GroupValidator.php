<?php
namespace Wandu\Validator;

use ArrayAccess;
use Traversable;
use Wandu\Validator\Contracts\ValidatorInterface;

class ArrayableValidator implements ValidatorInterface
{
    /** @var array */
    protected $validators = [];

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        if (!is_array($item) && !($item instanceof ArrayAccess && $item instanceof Traversable)) {
            return false;
        }
        
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($item)
    {
        return $this->validate($item);
    }
}