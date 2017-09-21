<?php
namespace Wandu\Sanitizer\Sample;

use Wandu\Sanitizer\Contracts\Rule;

class SamplePointRule implements Rule
{
    public function rule(): array
    {
        return [
            "name" => "string",
            "address?" => "string",
            "lat?" => "float",
            "lng?" => "float",
        ];
    }

    public function map(array $attributes = [])
    {
        return new SamplePoint($attributes);
    }
}
