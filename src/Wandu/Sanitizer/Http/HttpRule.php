<?php
namespace Wandu\Sanitizer\Http;

use Wandu\Http\Parameters\Parameter;
use Wandu\Sanitizer\Contracts\Rule;

abstract class HttpRule implements Rule
{
    /**
     * @param array $attributes
     * @return \Wandu\Http\Parameters\Parameter
     */
    public function map(array $attributes = [])
    {
        return new Parameter($attributes);
    }
}
