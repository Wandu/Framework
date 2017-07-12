<?php
namespace Wandu\Config;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wandu\Config\Exception\NotAllowedMethodException;
use Wandu\Config\Loader\EnvLoader;
use Wandu\Config\Loader\JsonLoader;
use Wandu\Config\Loader\PhpLoader;
use Wandu\Config\Loader\YmlLoader;

class ConfigTest extends TestCase
{
    public function testGet()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        static::assertSame('foo string!', $config->get('foo'));
        static::assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $config->get('bar'));
        static::assertSame('bar1 string!', $config->get('bar.bar1'));

        static::assertSame(null, $config->get('null'));
        static::assertSame(null, $config->get('null.isnull'));
    }

    public function testGetDefault()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        static::assertNull($config->get('bar.bar3'));
        static::assertNull($config->get('unknown.something'));

        static::assertSame('unknown', $config->get('bar.bar3', 'unknown'));
        static::assertSame('unknown', $config->get('unknown.something', 'unknown'));

        static::assertSame(null, $config->get('null', 'unknown'));
        static::assertSame('unknown', $config->get('null.isnull', 'unknown'));
    }

    public function testHas()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        static::assertTrue($config->has('foo'));
        static::assertTrue($config->has('bar'));

        static::assertTrue($config->has('bar.bar1'));
        static::assertFalse($config->has('bar.bar3'));

        static::assertTrue($config->has('null'));
        static::assertFalse($config->has('null.isnull'));
    }
    
    public function testArrayAccess()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        static::assertSame('foo string!', $config['foo']);
        static::assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $config['bar']);
        static::assertSame('bar1 string!', $config['bar.bar1']);

        static::assertSame(null, $config['null']);
        static::assertSame(null, $config['null.isnull']);

        // with default
        static::assertSame('unknown', $config['bar.bar3||unknown']);
        static::assertSame('unknown', $config['unknown.something||unknown']);

        static::assertSame(null, $config['null||unknown']);
        static::assertSame('unknown', $config['null.isnull||unknown']);
    }
    
    public function testReadOnly()
    {
        $config = new Config([
            'foo' => 'foo string!',
        ]);
        
        $config->get('foo');
        try {
            $config->offsetSet('bar', 'something');
            static::fail();
        } catch (NotAllowedMethodException $e) {
            static::addToAssertionCount(1);
        }
        try {
            $config->offsetUnset('foo');
            static::fail();
        } catch (NotAllowedMethodException $e) {
            static::addToAssertionCount(1);
        }
    }
    
    public function testSubset()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        static::assertInstanceOf(Config::class, $config->subset('bar'));
        static::assertEquals([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $config->subset('bar')->get(''));

        try {
            $config->subset('foo');
            static::fail();
        } catch (InvalidArgumentException $e) {}
        try {
            $config->subset('null');
            static::fail();
        } catch (InvalidArgumentException $e) {}
        try {
            $config->subset('bar.unknown');
            static::fail();
        } catch (InvalidArgumentException $e) {}
    }
    
    public function testMerge()
    {
        $config = new Config([
            'string1' => 'string1..',
            'string2' => 'string2..',
            'object1' => [
                'object11' => 'object11..',
                'array1' => [1, 2, 3,],
                'array2' => [2, 3, 4,],
            ],
            'object2' => [
                'object21' => 'object21..',
                'array2' => [2, 3, 4,],
            ],
            'object3' => [
                'object21' => 'object21..',
                'array2' => [2, 3, 4,],
            ],
        ]);
        $config->merge([
            'string2' => 'string2 overwrite',
            'string3' => 'string3 append',
            'object1' => [
                'object11' => 'object11 overwrite',
                'object12' => 'object12 append',
                'array1' => [3, 4, 5, ], // list -> list overwrite
                'array2' => [
                    'array21' => 'array21 overwrite',
                    'array22' => 'array22 overwrite',
                ], // list -> map overwrite 
            ], // map -> map merge
            'object2' => [1, 2, 3, 4, ], // map -> list overwrite
            'object3' => 'scalar', // object -> scalar overwrite
        ]);
        static::assertSame([
            'string1' => 'string1..',
            'string2' => 'string2 overwrite',
            'object1' => [
                'object11' => 'object11 overwrite',
                'array1' => [3, 4, 5, ], // list -> list overwrite
                'array2' => [
                    'array21' => 'array21 overwrite',
                    'array22' => 'array22 overwrite',
                ], // list -> map overwrite 
                'object12' => 'object12 append',
            ],
            'object2' => [1, 2, 3, 4, ], // map -> list overwrite
            'object3' => 'scalar', // object -> scalar overwrite
            'string3' => 'string3 append',
        ], $config->toArray());
    }

    public function testMergeDeep()
    {
        $config = new Config([
            'object1' => [
                'object2' => [
                    'object3' => [
                        'object4' => [
                            'object5' => [
                                'remain' => 'remain',
                                'object6' => [1, 2, 3, 4],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        $config->merge([
            'object1' => [
                'object2' => [
                    'object3' => [
                        'object4' => [
                            'object5' => [
                                'object6' => [
                                    'object7' => 'scalar',
                                ],
                                'object6-1' => 'object6-1..',
                            ],
                            'object5-1' => 'object5-1..',
                        ],
                    ],
                ],
                'object2-1' => 'object2-1..',
            ],
        ]);
        static::assertSame([
            'object1' => [
                'object2' => [
                    'object3' => [
                        'object4' => [
                            'object5' => [
                                'remain' => 'remain',
                                'object6' => [
                                    'object7' => 'scalar',
                                ],
                                'object6-1' => 'object6-1..',
                            ],
                            'object5-1' => 'object5-1..',
                        ],
                    ],
                ],
                'object2-1' => 'object2-1..',
            ],
        ], $config->toArray());
    }
    
    public function testWithLoader()
    {
        $config = new Config();

        $config->pushLoader(new PhpLoader());
        $config->pushLoader(new JsonLoader());
        $config->pushLoader(new EnvLoader());
        $config->pushLoader(new YmlLoader());

        $config->load(__DIR__ . '/test.config.php');
        $config->load(__DIR__ . '/test.config.json');
        $config->load(__DIR__ . '/test.config.env');
        $config->load(__DIR__ . '/test.config.yml');
        
        static::assertSame([
            'foo' => 'foo string',
            'vendor1' => [
                'service1' => [
                    'name' => 'vendor1 service1 name..',
                    'path' => 'vendor1 service1 path..',
                ],
                'service2' => [
                    'name' => 'vendor1 service2 name..',
                    'path' => 'vendor1 service2 path..',
                ],
            ],
            'vendor2' => [
                'service1' => [
                    'name' => 'vendor2 service1 name..',
                    'path' => 'vendor2 service1 path..',
                ],
                'service2' => [
                    'name' => 'vendor2 service2 name..',
                    'path' => 'vendor2 service2 path..',
                ],
            ],
            'json1' => 'json 1 string',
            'json2' => [
                'json2-1',
                'json2-2',
            ],
            'env1' => 'what the',
            'env2' => false,
            'yml1' => [
                'yml11' => true,
            ],
            'yml2' => [
                'paths' => ['vendor/*', 'tests/*']
            ],
            'yml3' => [
                'yml3_1',
                'yml3_2',
            ],
        ], $config->toArray());
    }
}
