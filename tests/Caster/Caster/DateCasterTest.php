<?php
namespace Wandu\Caster\Caster;

use PHPUnit_Framework_TestCase;
use Wandu\DateTime\Date;

class DateCasterTest extends PHPUnit_Framework_TestCase
{
    public function testCast()
    {
        $caster = new DateCaster();
        static::assertEquals(
            new Date('2017-03-12'),
            $caster->cast('2017-03-12')
        );
    }
}
