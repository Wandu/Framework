<?php
namespace Wandu\DateTime;

use Carbon\Carbon;
use PHPUnit_Framework_TestCase;

class DateTest extends PHPUnit_Framework_TestCase
{
    public function provideTimes()
    {
        return [
            [[2017, 1, 1], [2017, 1, 1]],
            [[2017, 2, 27], [2017, 2, 27]],
            [[2017, 2, 28], [2017, 2, 28]],
            [[2017, 2, 29], [2017, 3, 1]], // next
            [[2017, 12, 31], [2017, 12, 31]],
            [[2017, 12, 32], [2018, 1, 1]],

            [[2017, 0, 1], [2016, 12, 1]],
            [[2017, -1, 1], [2016, 11, 1]],

            [[2017, 1, 0], [2016, 12, 31]],
            [[2017, 1, -1], [2016, 12, 30]],
        ];
    }

    /**
     * @dataProvider provideTimes
     */
    public function testTime($input, $output)
    {
        $time = Date::fromDates($input[0], $input[1], $input[2]);

        static::assertSame($output[0], $time->year());
        static::assertSame($output[1], $time->month());
        static::assertSame($output[2], $time->day());
    }

    public function testToString()
    {
        $time = Date::fromDates(2017, 3, 4);
        static::assertSame('2017-03-04', $time->__toString());
    }

    public function testFromText()
    {
        $time = Date::fromText('2017-01-01');
        static::assertSame('2017-01-01', $time->__toString());
    }

    public function testFromCarbon()
    {
        $time = Date::fromCarbon(new Carbon('2017-04-01 12:00:00'));
        static::assertSame('2017-04-01', $time->__toString());

        $time = Date::fromCarbon(new Carbon('2017-04-01 00:00:00'));
        static::assertSame('2017-04-01', $time->__toString());
    }
}
