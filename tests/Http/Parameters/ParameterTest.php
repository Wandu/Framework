<?php
namespace Wandu\Http\Parameters;

use PHPUnit_Framework_TestCase;
use Wandu\Support\Exception\CannotCallMethodException;

class ParameterTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param1;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param2;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param3;

    public function setUp()
    {
        $this->param1 = new Parameter([
            'string' => 'string!',
            'number' => '10',
        ]);
        $this->param2 = new Parameter([
            'null' => null,
            'empty' => '',
            'false' => false,
        ]);
        $this->param3 = new Parameter([
            'string1' => 'string 1!',
            'string2' => 'string 2!',
        ], new Parameter([
            'string1' => 'string 1 fallback!',
            'fallback' => 'fallback!',
        ]));
    }

    public function testGet()
    {
        $params = $this->param1;

        static::assertSame('string!', $params->get('string'));
        static::assertSame('10', $params->get('number'));

        static::assertNull($params->get('string.undefined'));
        static::assertNull($params->get('number.undefined'));
    }

    public function testGetNull()
    {
        $params = $this->param2;

        // not strict
        static::assertEquals("Other Value!!", $params->get('undefined', "Other Value!!"));
        static::assertEquals("Other Value!!", $params->get('null', "Other Value!!"));
        static::assertEquals("Other Value!!", $params->get('empty', "Other Value!!"));
        static::assertEquals("Other Value!!", $params->get('false', "Other Value!!"));

        // strict
        static::assertEquals("Other Value!!", $params->get('undefined', "Other Value!!", true));
        static::assertEquals("Other Value!!", $params->get('null', "Other Value!!", true));
        static::assertEquals("", $params->get('empty', "Other Value!!", true));
        static::assertEquals(false, $params->get('false', "Other Value!!", true));
    }

    public function testHas()
    {
        $params = $this->param1;

        static::assertTrue($params->has('string'));
        static::assertTrue($params->has('number'));

        static::assertFalse($params->has('string.undefined'));
        static::assertFalse($params->has('number.undefined'));
    }

    public function testHasNull()
    {
        $params = $this->param2;

        static::assertFalse($params->has('undefined'));
        static::assertFalse($params->has('null'));
        static::assertTrue($params->has('empty'));
        static::assertTrue($params->has('false'));
    }

    public function testToArray()
    {
        $params = $this->param2;

       static::assertSame([
           'null' => null,
           'empty' => '',
           'false' => false,
        ], $params->toArray());
    }

    public function testToArrayWithFallback()
    {
        $params = $this->param3;

       static::assertSame([
            'string1' => 'string 1!',
            'string2' => 'string 2!',
            'fallback' => 'fallback!',
        ], $params->toArray());
    }

    public function testGetWithDefault()
    {
        $params = $this->param1;

        static::assertSame("default", $params->get('string.undefined', "default"));
        static::assertSame("default", $params->get('number.undefined', "default"));
    }

    public function testFallback()
    {
        $params = $this->param3;

        static::assertSame("string 1!", $params->get('string1'));
        static::assertSame("string 2!", $params->get('string2'));
        static::assertSame("fallback!", $params->get('fallback'));
        static::assertSame(null, $params->get('undefined'));

        static::assertSame("string 1!", $params->get('string1', "default"));
        static::assertSame("string 2!", $params->get('string2', "default"));
        static::assertSame("fallback!", $params->get('fallback', "default"));
        static::assertSame("default", $params->get('undefined', "default"));
    }

    public function testHasWithFallback()
    {
        $params = $this->param3;

        static::assertTrue($params->has('string1'));
        static::assertTrue($params->has('string2'));
        static::assertTrue($params->has('fallback'));
        static::assertFalse($params->has('undefined'));
    }

    public function testGetMany()
    {
        $params = $this->param1;

       static::assertSame(
            [
                'string' => 'string!',
                'number' => '10'
            ],
            $params->getMany(['string', 'number'])
        );

       static::assertSame(
            [
                'string' => 'string!',
            ],
            $params->getMany(['string'])
        );

       static::assertSame(
            [
                'string' => 'string!',
                'unknown' => null,
            ],
            $params->getMany(['string', 'unknown'])
        );

       static::assertSame(
            [
                'string' => 'string!',
                'unknown' => null,
            ],
            $params->getMany(['string', 'unknown' => null])
        );

       static::assertSame(
            [
                'string' => 'string!',
                'unknown' => false,
            ],
            $params->getMany(['string' => false, 'unknown' => false])
        );
    }

    public function testGetManyWithNull()
    {
        $params = $this->param2;

        static::assertSame(
            [
                'undefined' => null,
                'null' => null,
                'false' => null,
                'empty' => null,
            ],
            $params->getMany(['undefined', 'null', 'false', 'empty'])
        );

        static::assertSame(
            [
                'undefined' => null,
                'null' => null,
                'false' => false,
                'empty' => '',
            ],
            $params->getMany(['undefined', 'null', 'false', 'empty'], true)
        );

//        static::assertSame(
//            [
//                'string' => 'string!',
//            ],
//            $params->getMany(['string'])
//        );
//
//        static::assertSame(
//            [
//                'string' => 'string!',
//            ],
//            $params->getMany(['string', 'unknown'])
//        );
//
//        static::assertSame(
//            [
//                'string' => 'string!',
//                'unknown' => null,
//            ],
//            $params->getMany(['string', 'unknown' => null])
//        );
//
//        static::assertSame(
//            [
//                'string' => 'string!',
//                'unknown' => false,
//            ],
//            $params->getMany(['string' => false, 'unknown' => false])
//        );
    }

    public function testArrayAccess()
    {
        /** @var \Wandu\Http\Contracts\ParameterInterface $params */
        $params = $this->param1;

        static::assertSame('string!', $params['string']);

        static::assertSame(null, $params['unknown']);
        static::assertSame('default', $params['unknown||default']);

        static::assertTrue(isset($params['string']));
        static::assertFalse(isset($params['unknown']));

        try {
            $params['string'] = 'string?';
            static::fail();
        } catch (CannotCallMethodException $e) {
            static::assertEquals('offsetSet', $e->getMethodName());
        }
        try {
            unset($params['string']);
            static::fail();
        } catch (CannotCallMethodException $e) {
            static::assertEquals('offsetUnset', $e->getMethodName());
        }
    }
}
