<?php
namespace Wandu\View\Phiew;

use PHPUnit_Framework_TestCase;
use Wandu\View\FileNotFoundException;

class FileResolverTest extends PHPUnit_Framework_TestCase
{
    public function testResolveSuccess()
    {
        $env = new Configuration([
            'path' => __DIR__ . '/../views' 
        ]);
        $resolver = new FileResolver($env);

        static::assertInstanceOf(Template::class, $resolver->resolve('home.php'));
    }

    public function testResolveFail()
    {
        $env = new Configuration([
            'path' => __DIR__ . '/../views'
        ]);
        $resolver = new FileResolver($env);

        try {
            $resolver->resolve('unknown.php');
            static::fail();
        } catch (FileNotFoundException $e) {
            static::assertEquals('Cannot find the template file named \'unknown.php\'.', $e->getMessage());
        }
    }
}
