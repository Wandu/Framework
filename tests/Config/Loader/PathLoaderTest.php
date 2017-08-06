<?php
namespace Wandu\Config\Loader;

use PHPUnit\Framework\TestCase;
use Wandu\Config\Config;

class PathLoaderTest extends TestCase
{
    public function testLoad()
    {
        $config = new Config();
        
        $config->pushLoader(new PathLoader([
            new EnvLoader(),
            new JsonLoader(),
            new PhpLoader(),
            new YmlLoader()
        ]));
        
        $config->load(__DIR__ . '/..');
        
        static::assertEquals([
            'test_env' => [
                'env1' => 'what the',
                'env2' => false,
            ],
            'test_json' => [
                'json1' => 'json 1 string',
                'json2' => [
                    'json2-1',
                    'json2-2',
                ],
            ],
            'test_php' => [
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
            ],
            'test_yml' => [
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
            ],
            'test_path' => [
                'app' => [
                    'debug' => true,
                    'env' => 'test',
                ],
            ],
        ], $config->toArray());
    }
}
