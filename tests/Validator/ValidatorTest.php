<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\Validator\Contracts\RuleDefinitionInterface;
use Wandu\Validator\Contracts\RuleInterface;
use Wandu\Validator\Exception\InvalidValueException;

class ValidatorTest extends TestCase
{
    use Assertions;

    public function testRuleAssert()
    {
        $validator = new Validator(new ValidatorTestPointRule());

        $validator->assert(["name" => "wandu"]);
        $validator->assert([
            "name" => "wandu",
            "address" => "seoul",
            "lat" => 30.33333,
            "lng" => 127.00000,
        ]);

        static::assertException(new InvalidValueException(["required@name"]), function () use ($validator) {
            $validator->assert([]);
        });
        static::assertException(new InvalidValueException([
            "required@name", "string@address", "float@lat", "float@lng",
        ]), function () use ($validator) {
            $validator->assert([
                "address" => 30,
                "lat" => "lat!",
                "lng" => "lng!",
            ]);
        });
    }

    public function testRuleByRuleAssert()
    {
        $validator = new Validator(new ValidatorTestCharterRule());

        $validator->assert([
            "departure" => ["name" => "busan"],
            "arrival" => ["name" => "seoul"],
            "waypoints" => [],
            "timeToGo" => "2017-10-10 00:00:00",
            "people" => 50,
        ]);
        static::assertException(new InvalidValueException([
            "required@departure", "required@arrival", "required@waypoints", "required@timeToGo", "required@people",
        ]), function () use ($validator) {
            $validator->assert([]);
        });

        static::assertException(new InvalidValueException([
            "required@waypoints.1.name",
        ]), function () use ($validator) {
            $validator->assert([
                "departure" => ["name" => "busan"],
                "arrival" => ["name" => "seoul"],
                "waypoints" => [
                    ["name" => "seoul"], [], ["name" => "seoul"],
                ],
                "timeToGo" => "2017-10-10 00:00:00",
                "people" => 50,
            ]);
        });
    }
}

class ValidatorTestPointRule implements RuleInterface
{
    public function define(RuleDefinitionInterface $rule)
    {
        $rule->prop("name", "string");
        $rule->prop("address?", "string");
        $rule->prop("lat?", "float");
        $rule->prop("lng?", "float");
    }
}

class ValidatorTestCharterRule implements RuleInterface
{
    public function define(RuleDefinitionInterface $rule)
    {
        $rule->prop("departure", new ValidatorTestPointRule());
        $rule->prop("arrival", new ValidatorTestPointRule());
        $rule->prop("waypoints[]", new ValidatorTestPointRule());
        $rule->prop("timeToGo", "string");
        $rule->prop("timeToBack?", "string");
        $rule->prop("people", "int");
    }
}
