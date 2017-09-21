<?php
namespace Wandu\Validator\Sample;

use Wandu\Validator\Contracts\Rule;

class SampleCharterRule implements Rule
{
    public function definition(): array
    {
        return [
            "departure" => new SamplePointRule(),
            "arrival" => function () {
                return [
                    "name" => "string",
                    "address?" => "string",
                    "lat?" => "float",
                    "lng?" => "float",
                ];
            },
            "waypoints[]" => new SamplePointRule(),
            "timeToGo" => "int",
            "timeToBack?" => ["int", "greater_than:timeToGo"],
            "people" => "int",
        ];
    }
}
