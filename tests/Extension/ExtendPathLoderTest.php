<?php
namespace Wandu\DI\Extension;

use Mockery;
use Wandu\DI\TestCase;

class ExtendPathTest extends TestCase
{
    public function testPathLoad()
    {
        $this->container->closure(ExtendPath::class, function () {
            return new ExtendPath(dirname(__DIR__).'/Stub');
        });

        // how to works.
        $pathLoader = $this->container->get(ExtendPath::class);

        $this->assertEquals(
            dirname(__DIR__).'/Stub/myfile.php',
            call_user_func($pathLoader, '/myfile.php')
        );

        // more simple way.
        $this->container->alias('path', ExtendPath::class);
        $this->assertEquals(
            dirname(__DIR__).'/Stub/myfile.php',
            $this->container->path('/myfile.php')
        );
    }
}
