<?php
namespace Wandu\DI\Methods;

use PHPUnit_Framework_TestCase;
use stdClass;
use Wandu\DI\Container;

class InjectTest extends PHPUnit_Framework_TestCase
{
    public function testInject()
    {
        $container = new Container();

        $example = new InjectTestExample();

        static::assertNull($example->getSomething()); // null
        static::assertNull($example->getOtherthing()); // null

        // inject object
        $container->inject($example, [
            'something' => $something = new stdClass(),
        ]);

        static::assertSame($something, $example->getSomething()); // same

        // inject scalar
        $container->inject($example, [
            'otherthing' => $otherthing = 303030,
        ]);

        static::assertSame($something, $example->getSomething()); // same
        static::assertSame($otherthing, $example->getOtherthing()); // same
    }
}

class InjectTestExample
{
    private $something;

    private $otherthing;

    public function getSomething()
    {
        return $this->something;
    }

    public function getOtherthing()
    {
        return $this->otherthing;
    }
}
