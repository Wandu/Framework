<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\Validator\Contracts\RuleDefinition;
use Wandu\Validator\Contracts\Rule;
use Wandu\Validator\Exception\InvalidValueException;

class ValidatorTest extends TestCase
{
    use Assertions;

    /** @var \Wandu\Validator\ValidatorFactory */
    protected $validator;
    
    public function setUp()
    {
        $this->validator = new ValidatorFactory();
    }
    
    public function testStringAssert()
    {
        static::assertException(new InvalidValueException(["string"]), function () {
            $this->validator->create("string")->assert(1010);
        });
    }

    public function testRuleAssert()
    {
        $validator = $this->validator->create(new ValidatorTestPointRule());

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
        $validator = $this->validator->create(new ValidatorTestCharterRule());

        $validator->assert([
            "departure" => ["name" => "busan"],
            "arrival" => ["name" => "seoul"],
            "waypoints" => [],
            "timeToGo" => 1496139000,
            "timeToBack" => 1496139010,
            "people" => 50,
        ]);
        static::assertException(new InvalidValueException([
            "required@departure", "required@arrival", "required@waypoints", "required@timeToGo", "required@people",
        ]), function () use ($validator) {
            $validator->assert([]);
        });

        static::assertException(new InvalidValueException([
            "required@waypoints.1.name",
            "greater_than:timeToGo@timeToBack",
        ]), function () use ($validator) {
            $validator->assert([
                "departure" => ["name" => "busan"],
                "arrival" => ["name" => "seoul"],
                "waypoints" => [
                    ["name" => "seoul"], [], ["name" => "seoul"],
                ],
                "timeToGo" => 1496139000,
                "timeToBack" => 1496138000,
                "people" => 50,
            ]);
        });
    }
}

class ValidatorTestPointRule implements Rule
{
    public function define(RuleDefinition $rule)
    {
        $rule->prop("name", "string");
        $rule->prop("address?", "string");
        $rule->prop("lat?", "float");
        $rule->prop("lng?", "float");
    }
}

class ValidatorTestCharterRule implements Rule
{
    public function define(RuleDefinition $rule)
    {
        $rule->prop("departure", new ValidatorTestPointRule());
        $rule->prop("arrival", function (RuleDefinition $rule) {
            $rule->prop("name", "string");
            $rule->prop("address?", "string");
            $rule->prop("lat?", "float");
            $rule->prop("lng?", "float");
        });
        $rule->prop("waypoints[]", new ValidatorTestPointRule());
        $rule->prop("timeToGo", "int");
        $rule->prop("timeToBack?", "int", "greater_than:timeToGo");
        $rule->prop("people", "int");
    }
}
