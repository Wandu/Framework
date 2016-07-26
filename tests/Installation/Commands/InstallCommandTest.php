<?php
namespace Wandu\Installation\Commands;

use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Wandu\DI\Container;

class InstallCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->deleteAll(__DIR__ . '/project');
        mkdir(__DIR__ . '/project');
        copy(__DIR__ . '/composer.json', __DIR__ . '/project/composer.json');
        $process = new Process('composer install', __DIR__ . '/project');
        $process->setTimeout(300);
        $process->run();
    }
    
    public function tearDown()
    {
        Mockery::close();
    }

    protected function deleteAll($directory)
    {
        if (!is_dir($directory)) {
            return;
        }
        foreach (new \DirectoryIterator($directory) as $file) {
            if ($file->isDot()) continue;
            if ($file->isDir()) {
                $this->deleteAll($file->getRealPath());
            } elseif ($file->isLink()) {
                unlink($directory . '/' . $file->getFilename());
            } else {
                unlink($file->getRealPath());
            }
        }
        return rmdir($directory);
    }

    public function testInstallDefault()
    {
        $appNamespace = 'Wandu\\Test' . rand(0, 200);
        
        $io = Mockery::mock(SymfonyStyle::class);
        $io->shouldReceive('ask')
            ->with('install path?', Mockery::any())
            ->andReturn(__DIR__ . '/project'); // default
        $io->shouldReceive('ask')
            ->with('app namespace?', Mockery::any(), Mockery::any())
            ->andReturn($appNamespace); // change
        
        $this->install($io);
        
        // check composer
        $composer = json_decode(file_get_contents(__DIR__ . '/project/composer.json'), true);
        
        $this->assertEquals([$appNamespace . '\\' => "src/"], $composer['autoload']['psr-4']);
        
        // check .wandu.config.php
        $this->assertEquals([
            'env' => 'develop',
            'debug' => true,
            'timezone' => 'UTC',
            'database' => [
                'connections' => [
                    'default' => [
                        'driver'    => 'mysql',
                        'host'      => 'localhost',
                        'database'  => 'wandu',
                        'username'  => 'root',
                        'password'  => 'root',
                        'charset'   => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix'    => 'local_',
                    ],
                ],
                'migration' => [
                    'path' => 'migrations',
                ],
            ],
            'session' => [
                'type' => 'file',
                'path' => 'cache/sessions',
            ],
            'log' => [
                'path' => null,
            ],
            'view' => [
                'path' => 'views',
                'cache' => 'cache/views',
            ],
        ], require __DIR__ . '/project/.wandu.config.php');
    }

    public function testInstallOtherPath()
    {
        $appNamespace = 'Wandu\\Test' . rand(0, 200);

        $io = Mockery::mock(SymfonyStyle::class);
        $io->shouldReceive('ask')
            ->with('install path?', Mockery::any())
            ->andReturn('wandu'); // other path
        $io->shouldReceive('ask')
            ->with('app namespace?', Mockery::any(), Mockery::any())
            ->andReturn($appNamespace);

        $this->install($io);

        // check composer
        $composer = json_decode(file_get_contents(__DIR__ . '/project/composer.json'), true);

        $this->assertEquals([$appNamespace . '\\' => "wandu/src/"], $composer['autoload']['psr-4']);

        // check .wandu.config.php
        $this->assertEquals([
            'env' => 'develop',
            'debug' => true,
            'timezone' => 'UTC',
            'database' => [
                'connections' => [
                    'default' => [
                        'driver'    => 'mysql',
                        'host'      => 'localhost',
                        'database'  => 'wandu',
                        'username'  => 'root',
                        'password'  => 'root',
                        'charset'   => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix'    => 'local_',
                    ],
                ],
                'migration' => [
                    'path' => 'wandu/migrations',
                ],
            ],
            'session' => [
                'type' => 'file',
                'path' => 'wandu/cache/sessions',
            ],
            'log' => [
                'path' => null,
            ],
            'view' => [
                'path' => 'wandu/views',
                'cache' => 'wandu/cache/views',
            ],
        ], require __DIR__ . '/project/.wandu.config.php');
    }
    
    protected function install($io)
    {
        $input = Mockery::mock(InputInterface::class);
        $output = Mockery::mock(OutputInterface::class);

        $container = new Container();
        $container['base_path'] = __DIR__ . '/project';

        $output->shouldReceive('writeln');
        $output->shouldReceive('write');

        $command = new InstallCommand($container);
        \Wandu\Foundation\app()->inject($command, [
            'input' => $input,
            'output' => $output,
            'io' => $io,
        ]);

        $command->execute();

        if (file_exists(__DIR__ . '/project/vendor/bin/wandu')) {
            $process = new Process('php vendor/bin/wandu', __DIR__ . '/project');
            $process->run();

            $this->assertTrue(
                $process->isSuccessful(),
                $process->getOutput() . "\n" .
                $process->getErrorOutput()
            ); // it always success...
        } else {
            $this->markTestSkipped('vendor/bin/wandu file is not exists.');
        }

        $this->assertFileExists(__DIR__ . '/project/.wandu.php'); // it always in this
        $this->assertFileExists(__DIR__ . '/project/.wandu.config.php'); // it always in this
    }
}
