<?php
namespace Wandu\Database\Console;

use DirectoryIterator;
use Illuminate\Database\Capsule\Manager;
use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wandu\Config\Config;

class MigrateCreateCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!is_dir(__DIR__ . '/migrations')) {
            mkdir(__DIR__ . '/migrations');
        }
    }
    
    public function tearDown()
    {
        foreach (new DirectoryIterator(__DIR__ . '/migrations') as $file) {
            if ($file->isDot()) continue;
            unlink($file->getRealPath());
        }
        rmdir(__DIR__ . '/migrations');
        Mockery::close();
    }
    
    public function testSuccessExecute()
    {
        define('WANDU_PATH', __DIR__);

        $input = Mockery::mock(InputInterface::class);
        $input->shouldReceive('getArgument')->once()->with('name')->andReturn('CreateTests');
        $output = Mockery::mock(OutputInterface::class);

        $output->shouldReceive('writeln')->once()->with(Mockery::on(function ($item) {
            $item = str_replace("<info>create</info> ./migrations/", '', $item);
            $item = str_replace('_CreateTests.php', '', $item);
            return preg_match('/^1[0-9][0-1][0-9][0-3][0-9]_[0-2][0-9][0-5][0-9][0-5][0-9]$/', $item) === 1;
        }));
        
        $command = (new MigrateCreateCommand(Mockery::mock(Manager::class), new Config([
            'database' => [
                'migration' => [
                    'path' => 'migrations'
                ]
            ]
        ])))->withIO($input, $output);

        $countOfFiles = iterator_count(new DirectoryIterator(WANDU_PATH . '/migrations'));
        
        $command->execute();
        
        // one file created! :-)
        $this->assertEquals(
            $countOfFiles + 1,
            iterator_count(new DirectoryIterator(WANDU_PATH . '/migrations'))
        );
    }
}
