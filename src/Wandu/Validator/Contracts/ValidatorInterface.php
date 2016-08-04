<?php
namespace Wandu\Validator\Contracts;

interface ValidatorInterface
{
    /**
     * @param mixed $item
     * @param bool $stopOnFail
     * @throw \Exception
     */
    public function assert($item, $stopOnFail = false);
    
    /**
     * @param mixed $item
     * @return boolean
     */
    public function validate($item);
}
