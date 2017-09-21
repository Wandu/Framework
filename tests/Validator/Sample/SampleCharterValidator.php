<?php
namespace Wandu\Validator\Sample;

use Wandu\Validator\SingleValidatorAbstract;

class SampleCharterValidator extends SingleValidatorAbstract
{
    public function rule()
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
