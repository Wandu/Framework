<?php
namespace Wandu\Http\Parameters;

use Mockery;
use PHPUnit\Framework\TestCase;
use Wandu\Http\Exception\CannotCallMethodException;

class ParameterTest extends TestCase
{
    /** @var array */
    protected $param1Attributes = [
        'string' => 'string!',
        'number' => '10',
        'array' => [
            'null' => null,
            'empty' => '',
            'false' => false,
        ],
        'array_of_array' => [
            [
                'string' => 'string!',
                'number' => '10',
            ],
            [
                'string' => 'string!!',
                'number' => '11',
            ],
            [
                'string' => 'string!!!',
                'number' => '12',
            ],
        ],
    ];
    
    /** @var array */
    protected $param2Attributes = [
        'null' => null,
        'empty' => '',
        'false' => false,
    ];
    
    /** @var array */
    protected $param3Attributes = [
        'string1' => 'string 1!',
        'string2' => 'string 2!',
    ];

    /** @var array */
    protected $param3FallbackAttributes = [
        'string1' => 'string 1 fallback!',
        'fallback' => 'fallback!',
    ];
    
    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param1;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param2;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param3;

    public function setUp()
    {
        $this->param1 = new Parameter($this->param1Attributes);
        $this->param2 = new Parameter($this->param2Attributes);
        $this->param3 = new Parameter($this->param3Attributes, new Parameter($this->param3FallbackAttributes));
    }
    
    public function tearDown()
    {
        Mockery::close();
    }

    public function testGet()
    {
        static::assertSame('string!', $this->param1->get('string'));
        static::assertSame('10', $this->param1->get('number'));

        static::assertNull($this->param1->get('string.undefined'));
        static::assertNull($this->param1->get('number.undefined'));
    }

    public function testGetNull()
    {
        // not strict
        static::assertEquals("Other Value!!", $this->param2->get('undefined', "Other Value!!"));
        static::assertEquals("Other Value!!", $this->param2->get('null', "Other Value!!"));
        static::assertEquals("Other Value!!", $this->param2->get('empty', "Other Value!!"));
        static::assertEquals("Other Value!!", $this->param2->get('false', "Other Value!!"));

        // strict
        static::assertEquals("Other Value!!", $this->param2->get('undefined', "Other Value!!", true));
        static::assertEquals("Other Value!!", $this->param2->get('null', "Other Value!!", true));
        static::assertEquals("", $this->param2->get('empty', "Other Value!!", true));
        static::assertEquals(false, $this->param2->get('false', "Other Value!!", true));
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
        static::assertFalse($this->param2->has('undefined'));
        static::assertFalse($this->param2->has('null'));
        static::assertTrue($this->param2->has('empty'));
        static::assertTrue($this->param2->has('false'));
    }

    public function testToArray()
    {
       static::assertSame([
           'null' => null,
           'empty' => '',
           'false' => false,
        ], $this->param2->toArray());
    }

    public function testToArrayWithFallback()
    {
       static::assertSame([
            'string1' => 'string 1!',
            'string2' => 'string 2!',
            'fallback' => 'fallback!',
        ], $this->param3->toArray());
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
    }

    public function testArrayAccessOffsetGet()
    {
        static::assertSame($this->param1->get('string'), $this->param1['string']);
        static::assertSame($this->param1->get('unknown'), $this->param1['unknown']);
        static::assertSame($this->param1->get('unknown', 'default'), $this->param1['unknown||default']);

        static::assertSame('string!', $this->param1['string']);
        static::assertSame(null, $this->param1['unknown']);
        static::assertSame('default', $this->param1['unknown||default']);
    }

    public function testArrayAccessOffsetExists()
    {
        static::assertSame($this->param1->has('string'), isset($this->param1['string']));
        static::assertSame($this->param1->has('unknown'), isset($this->param1['unknown']));

        static::assertTrue(isset($this->param1['string']));
        static::assertFalse(isset($this->param1['unknown']));
    }

    public function testArrayAccessOffsetSet()
    {
        try {
            $this->param1['string'] = 'string?';
            static::fail();
        } catch (CannotCallMethodException $e) {
            static::assertEquals('offsetSet', $e->getMethodName());
        }
    }

    public function testArrayAccessOffsetUnset()
    {
        try {
            unset($this->param1['string']);
            static::fail();
        } catch (CannotCallMethodException $e) {
            static::assertEquals('offsetUnset', $e->getMethodName());
        }
    }

    public function testGetIterator()
    {
        static::assertSame([
            'string',
            'number',
            'array',
            'array_of_array',
        ], array_keys(iterator_to_array($this->param1)));
        static::assertSame([
            'string!',
            '10',
            [
                'null' => null,
                'empty' => '',
                'false' => false,
            ],
            [
                [
                    'string' => 'string!',
                    'number' => '10',
                ],
                [
                    'string' => 'string!!',
                    'number' => '11',
                ],
                [
                    'string' => 'string!!!',
                    'number' => '12',
                ],
            ],
        ], array_values(iterator_to_array($this->param1)));
        
        // with fallback
        static::assertSame([
            'string1',
            'string2',
            'fallback',
        ], array_keys(iterator_to_array($this->param3)));
        static::assertSame([
            'string 1!',
            'string 2!',
            'fallback!',
        ], array_values(iterator_to_array($this->param3)));
    }

    public function testGetWithDotSyntax()
    {
        static::assertSame([
            'null' => null,
            'empty' => '',
            'false' => false,
        ], $this->param1->get('array'));

        // not strict
        static::assertEquals("Other Value!!", $this->param1->get('array.undefined', "Other Value!!"));
        static::assertEquals("Other Value!!", $this->param1->get('array.null', "Other Value!!"));
        static::assertEquals("Other Value!!", $this->param1->get('array.empty', "Other Value!!"));
        static::assertEquals("Other Value!!", $this->param1->get('array.false', "Other Value!!"));

        // strict
        static::assertEquals("Other Value!!", $this->param1->get('array.undefined', "Other Value!!", true));
        static::assertEquals("Other Value!!", $this->param1->get('array.null', "Other Value!!", true));
        static::assertEquals("", $this->param1->get('array.empty', "Other Value!!", true));
        static::assertEquals(false, $this->param1->get('array.false', "Other Value!!", true));
    }

    public function testHasWithDotSyntax()
    {
        static::assertTrue($this->param1->has('array'));

        static::assertFalse($this->param1->has('array.undefined'));
        static::assertFalse($this->param1->has('array.null'));
        static::assertTrue($this->param1->has('array.empty'));
        static::assertTrue($this->param1->has('array.false'));
    }
}
