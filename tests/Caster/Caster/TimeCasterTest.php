<?php
namespace Wandu\Caster\Caster;

use PHPUnit\Framework\TestCase;
use Wandu\DateTime\Time;

class TimeCasterTest extends TestCase
{
    public function testCast()
    {
        $caster = new TimeCaster();

        static::assertEquals(
            new Time(0),
            $caster->cast('00:00:00')
        );

        static::assertEquals(
            new Time(80),
            $caster->cast(80)
        );
        static::assertEquals(
            new Time(123),
            $caster->cast('123')
        );
    }
}
