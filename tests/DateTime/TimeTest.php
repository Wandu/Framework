<?php
namespace Wandu\DateTime;

use Carbon\Carbon;
use PHPUnit_Framework_TestCase;

class TimeTest extends PHPUnit_Framework_TestCase
{
    public function provideTimes()
    {
        return [
            [[0, 0, 0], [0, 0, 0]],
            [[3, 5, 20], [3, 5, 20]],
            [[23, 59, 59], [23, 59, 59]],
            [[24, 0, 0], [0, 0, 0]],

            [[-1, 0, 0], [23, 00, 0]],
            [[0, -1, 0], [23, 59, 0]],
            [[0, 0, -1], [23, 59, 59]],

            [[-1, -1, -1], [22, 58, 59]],

            [[25, 0, 0], [1, 0, 0]],
            [[0, 61, 0], [1, 1, 0]],
            [[0, 0, 61], [0, 1, 1]],

            [[25, 61, 61], [2, 2, 1]],
        ];
    }

    /**
     * @dataProvider provideTimes
     */
    public function testTime($input, $output)
    {
        $time = Time::fromTimes($input[0], $input[1], $input[2]);

        static::assertSame($output[0], $time->hours());
        static::assertSame($output[1], $time->minutes());
        static::assertSame($output[2], $time->seconds());
    }
    
    public function testToString()
    {
        $time = Time::fromTimes(0, 0, 0);
        static::assertSame('00:00:00', $time->__toString());
    }

    public function testFromText()
    {
        $time = Time::fromText('00:00:00');
        static::assertSame('00:00:00', $time->__toString());

        $time = Time::fromText('24:00:00');
        static::assertSame('00:00:00', $time->__toString());

        $time = Time::fromText('16:23:61');
        static::assertSame('16:24:01', $time->__toString());
    }

    public function testFromCarbon()
    {
        $time = Time::fromCarbon(new Carbon('2017-04-01 12:00:00'));
        static::assertSame('12:00:00', $time->__toString());

        $time = Time::fromCarbon(new Carbon('2017-04-01 00:00:00'));
        static::assertSame('00:00:00', $time->__toString());
    }

    public function testSetTimeZone()
    {
        $time = new Time(28800, 'UTC');
        static::assertSame('08:00:00', $time->__toString());

        $time->setTimezone('Asia/Seoul');
        static::assertSame('17:00:00', $time->__toString());

        $time->setTimezone('+05:00');
        static::assertSame('13:00:00', $time->__toString());

        $time->setTimezone('-09:00');
        static::assertSame('23:00:00', $time->__toString());
    }
}
