<?php
namespace Wandu\Router;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Router\ClassLoader\DefaultLoader;

class RouterTestCase extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Router\Router */
    protected $router;

    /** @var \Wandu\Router\Dispatcher */
    protected $dispatcher;

    public function setUp()
    {
        $this->router = new Router();
        $this->dispatcher = new Dispatcher(new DefaultLoader());
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
