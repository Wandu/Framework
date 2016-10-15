<?php
namespace Wandu\Installation;

use PHPUnit_Framework_TestCase;
use Wandu\Installation\Replacers\OriginReplacer;

class SkeletonBuilderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->deleteAll(__DIR__ . '/target');
        mkdir(__DIR__ . '/target');
        file_put_contents(__DIR__ . '/target/.gitignore', <<<GITIGNORE
composer.lock
others

GITIGNORE
        );
    }
    
    protected function deleteAll($directory)
    {
        if (!is_dir($directory)) return; 
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
            '___NAMESPACE___' => 'Wandu\\App',
            '___controller(\\d+)___' => function ($matches) use ($randNumber) {
                return 'User' . ($matches[1] + $randNumber);
            },
            '%%source%%' => new OriginReplacer(),
        ]);
        
        $this->assertTrue(is_dir(__DIR__ . '/target'));
        $this->assertTrue(is_dir(__DIR__ . '/target/cache'));
        
        $this->assertTrue(file_exists(__DIR__ . '/target/cache/.gitkeep'));

        $assertNumber = 30 + $randNumber;
        $this->assertEquals(<<<PHP
<?php
namespace Wandu\App;

class Hello
{
    public function getUser{$assertNumber}()
    {
        return 'user';
    }
}

PHP
        , file_get_contents(__DIR__ . '/target/Hello.php'));

        $this->assertEquals(<<<GITIGNORE
composer.lock
others


/new
/what

GITIGNORE
            , file_get_contents(__DIR__ . '/target/.gitignore'));
    }
}
