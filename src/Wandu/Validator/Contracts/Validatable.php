<?php
namespace Wandu\Validator\Contracts;

interface Validatable
{
    /**
     * @param mixed $data
     * @return void
     * @throws \Wandu\Validator\Exception\InvalidValueException
     */
    public function assert($data);

    /**
     * @param mixed $data
     * @return bool
     */
    public function validate($data): bool;
}
