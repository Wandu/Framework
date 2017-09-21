<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\Validator\Exception\InvalidValueException;
use Wandu\Validator\Sample\SampleCharterRule;
use Wandu\Validator\Sample\SamplePointRule;

class ValidatorTest extends TestCase
{
    use Assertions;

    /** @var \Wandu\Validator\ValidatorFactory */
    protected $validator;
    
    public function setUp()
    {
        $this->validator = new ValidatorFactory();
    }
    
    public function testStringRule()
    {
        static::assertException(new InvalidValueException(["string"]), function () {
            $this->validator->factory("string")->assert(1010);
        });
    }

    public function provideSimpleArrayRules()
    {
        return [
            [[
                "name" => "string",
                "address?" => "string",
                "lat?" => "float",
                "lng?" => "float",
            ],],
            [new SamplePointRule(),],
        ];
    }

    /**
     * @dataProvider provideSimpleArrayRules
     * @param mixed $rule
     */
    public function testSimpleArray($rule)
    {
        $validator = $this->validator->factory($rule);

        $validator->assert(["name" => "wandu"]);
        $validator->assert([
            "name" => "wandu",
            "address" => "seoul",
            "lat" => 30.33333,
            "lng" => 127.00000,
        ]);

        static::assertException(new InvalidValueException(["required@name"]), function () use ($validator) {
            $validator->assert('...');
        });
        static::assertException(new InvalidValueException(["required@name"]), function () use ($validator) {
            $validator->assert([]);
        });
        static::assertException(new InvalidValueException(["unknown@wrong"]), function () use ($validator) {
            $validator->assert([
                "name" => "wandu",
                "address" => "seoul",
                "lat" => 30.33333,
                "lng" => 127.00000,
                'wrong' => 'unknown data!',
            ]);
        });
        static::assertException(new InvalidValueException(["unknown@wrong", "unknown@wrong2"]), function () use ($validator) {
            $validator->assert([
                "name" => "wandu",
                "address" => "seoul",
                "lat" => 30.33333,
                "lng" => 127.00000,
                'wrong' => 'unknown data!',
                'wrong2' => 'unknown data!',
            ]);
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

    public function provideComplexArrayRules()
    {
        return [
            [[
                "departure" => [
                    "name" => "string",
                    "address?" => "string",
                    "lat?" => "float",
                    "lng?" => "float",
                ],
                "arrival" => function () {
                    return [
                        "name" => "string",
                        "address?" => "string",
                        "lat?" => "float",
                        "lng?" => "float",
                    ];
                },
                "waypoints[]" => new SamplePointRule(),
                "timeToGo" =>"int",
                "timeToBack?" => [
                    "int", "greater_than:timeToGo",
                ],
                "people" => "int",
            ],],
            [new SampleCharterRule(),],
        ];
    }

    /**
     * @dataProvider provideComplexArrayRules
     * @param mixed $rule
     */
    public function testComplexArray($rule)
    {
        $validator = $this->validator->factory($rule);
        $validator->assert([
            "departure" => [
                "name" => "busan",
            ],
            "arrival" => [
                "name" => "seoul",
            ],
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
            "required@waypoints[1].name",
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

    public function testMultiDemension()
    {
        $validator = $this->validator->factory([
            'users[]' => [
                'name' => 'string',
            ],
        ]);
        $validator->assert([
            'users' => [
                ['name' => 'wan2'],
                ['name' => 'wan3'],
                ['name' => 'wan4'],
            ]
        ]);
        $validator = $this->validator->factory([
            'users[][]' => [
                'name' => 'string',
            ],
        ]);
        /** @var \Wandu\Validator\Exception\InvalidValueException $exception */
        $exception = static::catchException(function () use ($validator) {
            $validator->assert([
                'users' => [
                    [['name' => 'wan2']],
                    [['wrong' => 'wan3']],
                    [['name' => 'wan4']],
                ]
            ]);
        });
        static::assertEquals(['required@users[1][0].name', 'unknown@users[1][0].wrong'], $exception->getTypes());
    }
}
