<?php
namespace Wandu\DI;

use Mockery;

class ReadmeBasicMethodsTest extends TestCase
{
    public function testSimpleExample()
    {
/* simple_example { */
$container = new \Wandu\DI\Container();

$container->instance('config', [
    'username' => 'db_username',
    'password' => 'db_password',
]);
$container->bind(DbAdapterInterface::class, MySqlAdapter::class);
$container->closure(Client::class, function (ContainerInterface $app) {
    return new Client(
        $app[DbAdapterInterface::class],
        $app['config']['username'],
        $app['config']['password']
    );
});
$container->alias('client', Client::class);

$container['client']; // return Client Object
$container[Client::class]; // return Client Object

// $container['client'] === $container[Client::class]
$this->assertSame(
    $container['client'],
    $container[Client::class]
);
/* } */
    }

    public function testInstanceExample()
    {
/* instance_example { */
$container = new \Wandu\DI\Container();

$container->instance('config1', [
    'username' => 'db_username',
    'password' => 'db_password',
]);

$this->assertEquals([
    'username' => 'db_username',
    'password' => 'db_password',
], $container['config1']);

// offsetSet
$container['config2'] = [
    'username' => 'db_username',
    'password' => 'db_password',
];

$this->assertEquals([
    'username' => 'db_username',
    'password' => 'db_password',
], $container['config2']);

/* } */
    }

    public function testClosureExample()
    {
/* closure_example { */
$container = new \Wandu\DI\Container();

$container->closure(DbAdapterInterface::class, function () {
    return new MySqlAdapter();
});

$this->assertInstanceOf(
    DbAdapterInterface::class,
    $container[DbAdapterInterface::class]
);

/* } */
    }

    public function testBindExample()
    {
/* bind_example { */
$container = new \Wandu\DI\Container();

$container->bind(DbAdapterInterface::class, MySqlAdapter::class);

$this->assertInstanceOf(
    DbAdapterInterface::class,
    $container[DbAdapterInterface::class]
);
$this->assertInstanceOf(
    MySqlAdapter::class,
    $container[MySqlAdapter::class]
);

// also same
$this->assertSame($container[DbAdapterInterface::class], $container[MySqlAdapter::class]);

/* } */
    }

    public function testAliasExample()
    {
/* alias_example { */
$container = new \Wandu\DI\Container();

$container->bind(DbAdapterInterface::class, MySqlAdapter::class);
$container->alias('adapter', DbAdapterInterface::class);

// get by alias name
$this->assertInstanceOf(
    DbAdapterInterface::class,
    $container['adapter']
);

/* } */
    }
}

interface DbAdapterInterface {}
class MySqlAdapter implements DbAdapterInterface {}
class Client {
    public function __construct(DbAdapterInterface $adapter, $username, $password) {}
}