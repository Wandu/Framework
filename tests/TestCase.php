<?php
namespace Wandu\DI;

use ArrayObject;
use Mockery;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\DI\ContainerInterface */
    protected $container;

    /** @var \ArrayObject */
    protected $configs;

    public function setUp()
    {
        parent::setUp();
        $this->configs = new ArrayObject([
            'database' => [
                'username' => 'username string',
                'password' => 'password string',
            ]
        ]);
        $this->container = new Container($this->configs);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
