Wandu Config
===

[![Latest Stable Version](https://poser.pugx.org/wandu/config/v/stable.svg)](https://packagist.org/packages/wandu/config)
[![Latest Unstable Version](https://poser.pugx.org/wandu/config/v/unstable.svg)](https://packagist.org/packages/wandu/config)
[![Total Downloads](https://poser.pugx.org/wandu/config/downloads.svg)](https://packagist.org/packages/wandu/config)
[![License](https://poser.pugx.org/wandu/config/license.svg)](https://packagist.org/packages/wandu/config)

Simple Config Based On Dot Array.

## Installation

```bash
composer require wandu/config
```

## Usage

```php
$config = new \Wandu\Config\Config([
    'host' => 'wandu.github.io',
    'log' => [
        'handler' => 'monolog',
        'path' => 'log/wandu.log',
    ],
]);

static::assertSame('wandu.github.io', $config->get('host'));
static::assertSame('wandu.github.io', $config['host']);

static::assertSame([
    'handler' => 'monolog',
    'path' => 'log/wandu.log',
], $config->get('log'));
static::assertSame([
    'handler' => 'monolog',
    'path' => 'log/wandu.log',
], $config['log']);

static::assertSame('log/wandu.log', $config->get('log.path'));
static::assertSame('log/wandu.log', $config['log']['path']);
static::assertSame('log/wandu.log', $config['log.path']); // you can use dot syntax in array!
```

### Use Default Value

```php
$config = new \Wandu\Config\Config([
    'debug' => false,
    'cache_dir' => null,
]);

static::assertSame(false, $config['debug']);
static::assertSame(null, $config['cache_dir']);
static::assertSame(null, $config['unknown']);

static::assertSame(false, $config->get('debug', true));
static::assertSame(null, $config->get('cache_dir', "/")); // check by array_key_exists
static::assertSame("unknown text..", $config->get('unknown', "unknown text.."));
```

### Support Loader

- PHP (example, [test_php.php](../../../tests/Config/test_php.php))
- JSON (example, [test_json.json](../../../tests/Config/test_json.json))
- Env(require `m1/env`) (example, [test_env.env](../../../tests/Config/test_env.env))
- YAML(require `symfony/yaml`) (example, [test_yml.yml](../../../tests/Config/test_yml.yml))

```php
$config = new \Wandu\Config\Config();

$config->pushLoader(new \Wandu\Config\Loader\PhpLoader());
$config->pushLoader(new \Wandu\Config\Loader\JsonLoader());
$config->pushLoader(new \Wandu\Config\Loader\EnvLoader());
$config->pushLoader(new \Wandu\Config\Loader\YmlLoader());
$config->pushLoader(new \Wandu\Config\Loader\PathLoader([
    new \Wandu\Config\Loader\YmlLoader(),
]));

$config->load(__DIR__ . '/test_php.php');
$config->load(__DIR__ . '/test_json.json');
$config->load(__DIR__ . '/test_env.env');
$config->load(__DIR__ . '/test_yml.yml');
$config->load(__DIR__ . '/test_path');

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
    'app' => [
        'debug' => true,
        'env' => 'test',
    ],
], $config->toArray());
```
