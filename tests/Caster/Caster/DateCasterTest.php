<?php
namespace Wandu\Caster\Caster;

use PHPUnit\Framework\TestCase;
use Wandu\DateTime\Date;

class DateCasterTest extends TestCase 
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
