<?php
namespace Wandu\Sanitizer\Contracts;

interface Rule
{
    /**
     * @return string|array|\Wandu\Validator\Contracts\Rule
     */
    public function rule();

    /**
     * @param array $attributes
     * @return object
     */
    public function map(array $attributes = []);
}
