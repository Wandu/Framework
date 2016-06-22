<?php
namespace Wandu\DI;

use Mockery;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\DI\ContainerInterface */
    protected $container;

    public function setUp()
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
