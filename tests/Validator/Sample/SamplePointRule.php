<?php
namespace Wandu\Validator\Sample;

use Wandu\Validator\Contracts\Rule;

class SamplePointRule implements Rule
{
    public function rules(): array
    {
        return [
            "name" => "string",
            "address?" => "string",
            "lat?" => "float",
            "lng?" => "float",
        ];
    }
}
