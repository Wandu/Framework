<?php
namespace Wandu\Validator\Contracts;

interface ValidatorInterface
{
    /**
     * @param mixed $item
     * @throw \Exception
     */
    public function assert($item);
    
    /**
     * @param mixed $item
     * @return boolean
     */
    public function validate($item);
}
