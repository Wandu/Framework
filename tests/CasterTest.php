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
            // output is type of Exception, then test Exception.
            if ($output instanceof \Exception) {
                try {
                    $this->caster->cast($input, $cast);
                    $this->fail();
                } catch (\Exception $e) {
                    $this->assertEquals($output->getMessage(), $e->getMessage());
                    $this->assertSame(get_class($output), get_class($e));
                }
            } else {
                $this->assertInstanceOf(
                    \stdClass::class,
                    $this->caster->cast($input, $cast)
                );
                $this->assertEquals($output, $this->caster->cast($input, $cast));
            }
        } else {
            $this->assertSame($output, $this->caster->cast($input, $cast));
        }
    }
}
