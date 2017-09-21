<?php
namespace Wandu\Sanitizer;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\Sanitizer\Sample\SampleCharter;
use Wandu\Sanitizer\Sample\SampleCharterRule;
use Wandu\Sanitizer\Sample\SamplePoint;
use Wandu\Sanitizer\Sample\SamplePointRule;

class SanitizerTest extends TestCase
{
    use Assertions;

    public function testSimple()
    {
        $factory = new SanitizerFactory();
        $sanitizer = $factory->factory(new SamplePointRule());

        /** @var \Wandu\Validator\Exception\InvalidValueException $exception */
        $exception = static::catchException(function () use ($sanitizer) {
            $sanitizer->sanitize([]);
        });
        static::assertEquals(['required@name'], $exception->getTypes());

        /** @var \Wandu\Sanitizer\Sample\SamplePoint $point */
        $point = $sanitizer->sanitize(['name' => 'Seoul, Korea']);

        static::assertInstanceOf(SamplePoint::class, $point);
        static::assertEquals(['name' => 'Seoul, Korea'], $point->getAttributes());
    }

    public function testRecursive()
    {
        $factory = new SanitizerFactory();
        $sanitizer = $factory->factory(new SampleCharterRule());

        /** @var \Wandu\Validator\Exception\InvalidValueException $exception */
        $exception = static::catchException(function () use ($sanitizer) {
            $sanitizer->sanitize([]);
        });
        static::assertEquals([
            'required@departure',
            'required@arrival',
            'required@waypoints',
            'required@timeToGo',
            'required@people',
        ], $exception->getTypes());

        /** @var \Wandu\Sanitizer\Sample\SampleCharter $charter */
        $charter = $sanitizer->sanitize([
            'departure' => ['name' => 'Seoul',],
            'arrival' => ['name' => 'Busan',],
            'waypoints' => [],
            'timeToGo' => 1,
            'people' => 40,
        ]);

        static::assertInstanceOf(SampleCharter::class, $charter);
        static::assertEquals([
            'departure' => new SamplePoint(['name' => 'Seoul',]),
            'arrival' => new SamplePoint(['name' => 'Busan',]),
            'waypoints' => [],
            'timeToGo' => 1,
            'timeToBack' => null,
            'people' => 40,
        ], $charter->getAttributes());
    }
}
