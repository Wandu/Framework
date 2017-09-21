<?php
namespace Wandu\Sanitizer\Sample;

use Wandu\Sanitizer\Contracts\Rule;

class SampleCharterRule implements Rule
{
    public function rule(): array
    {
        return [
            "departure" => SamplePointRule::class,
            "arrival" => SamplePointRule::class,
            "waypoints[]" => SamplePointRule::class,
            "timeToGo" => "int",
            "timeToBack?" => ["int", "greater_than:timeToGo"],
            "people" => "int",
        ];
    }

    public function map(array $attributes = [])
    {
        return new SampleCharter([
            'departure' => new SamplePoint($attributes['departure']),
            'arrival' => new SamplePoint($attributes['arrival']),
            'waypoints' => array_map(function ($waypoint) {
                return new SamplePoint($waypoint);
            }, $attributes['waypoints']),
            'timeToGo' => $attributes['timeToGo'],
            'timeToBack' => $attributes['timeToBack'] ?? null,
            'people' => $attributes['people'],
        ]);
    }
}
