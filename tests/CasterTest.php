<?php
namespace Wandu\Caster;

use Mockery;
use PHPUnit_Framework_TestCase;

class CasterTest extends PHPUnit_Framework_TestCase
{
    use CastProviderTrait;

    /** @var \Wandu\Caster\CasterInterface */
    protected $caster;

    public function setUp()
    {
        $this->caster = new Caster();
    }

    /**
     * @dataProvider castingProvider
     */
    public function testCasting($input, $cast, $output)
    {
        if (is_object($output)) {
            $this->assertInstanceOf(
                \stdClass::class,
                $this->caster->cast($input, $cast)
            );
            $this->assertEquals($output, $this->caster->cast($input, $cast));
        } else {
            $this->assertSame($output, $this->caster->cast($input, $cast));
        }
    }
}
