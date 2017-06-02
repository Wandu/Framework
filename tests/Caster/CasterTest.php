<?php
namespace Wandu\Caster;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Wandu\Caster\Caster\CarbonCaster;

class CasterTest extends TestCase 
{
    use CastProviderTrait;

    /** @var \Wandu\Caster\CastManagerInterface */
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
                    static::fail();
                } catch (\Exception $e) {
                    static::assertEquals($output->getMessage(), $e->getMessage());
                    static::assertSame(get_class($output), get_class($e));
                }
            } else {
                static::assertInstanceOf(
                    \stdClass::class,
                    $this->caster->cast($input, $cast)
                );
                static::assertEquals($output, $this->caster->cast($input, $cast));
            }
        } else {
            static::assertSame($output, $this->caster->cast($input, $cast));
        }
    }
    
    public function testAddCaster()
    {
        try {
            $this->caster->cast('2017-04-04', 'datetime');
            static::fail();
        } catch (UnsupportTypeException $e) {
            static::assertEquals('unsupport type "datetime".', $e->getMessage());
        }

        $this->caster->addCaster('datetime', new CarbonCaster('Asia/Seoul'));
        static::assertEquals(
            new Carbon('2017-04-04', 'Asia/Seoul'),
            $this->caster->cast('2017-04-04', 'datetime')
        );
    }
}
