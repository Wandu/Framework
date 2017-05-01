<?php
namespace Wandu\Router\Exception;

use RuntimeException;

class CannotGetPathException extends RuntimeException
{
    /** @var array */
    protected  $attributeKeys;
    
    public function __construct(array $attributeKeys = [])
    {
        $this->attributeKeys = $attributeKeys;
        $keys = implode(', ', $attributeKeys);
        $this->message = "cannot get path, at least {$keys} is required.";
    }
}
