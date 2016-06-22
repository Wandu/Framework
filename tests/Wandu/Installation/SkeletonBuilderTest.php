<?php
namespace Wandu\Installation;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Q\Queue;

class SkeletonBuilderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        $this->deleteAll(__DIR__ . '/target');
    }

    protected function deleteAll($directory)
    {
        foreach (new \DirectoryIterator($directory) as $file) {
            if ($file->isDot()) continue;
            if ($file->isDir()) {
                $this->deleteAll($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        return rmdir($directory);
    }

    public function testBuild()
    {
        $builder = new SkeletonBuilder(__DIR__ . '/target', __DIR__ . '/skeleton');
        $randNumber = rand(0, 100);
        $builder->build([
            'namespace' => 'Wandu\\App',
            'controller' => 'User' . $randNumber,
        ]);
        
        $this->assertTrue(is_dir(__DIR__ . '/target'));
        $this->assertTrue(is_dir(__DIR__ . '/target/cache'));
        
        $this->assertFalse(file_exists(__DIR__ . '/target/cache/.gitkeep'));
        
        $this->assertEquals(<<<PHP
<?php
namespace Wandu\App;

class Hello
{
    public function getUser{$randNumber}()
    {
        return 'user';
    }
}

PHP
        , file_get_contents(__DIR__ . '/target/Hello.php'));
    }
}
