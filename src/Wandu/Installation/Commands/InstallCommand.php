<?php
namespace Wandu\Installation\Commands;

use Symfony\Component\Console\Style\SymfonyStyle;
use Wandu\Console\Command;
use Wandu\Console\Reader;

class InstallCommand extends Command
{
    /** @var string */
    protected $description = "Install <comment>Wandu Framework</comment> to your project directory.";

    /** @var \Wandu\Console\Reader */
    protected $reader;
    
    /** @var \Symfony\Component\Console\Style\SymfonyStyle */
    protected $io;
    
    /**
     * @param \Wandu\Console\Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function execute()
    {
        // make io
        $this->io = new SymfonyStyle($this->input, $this->output);
        
        $this->output->writeln('Hello, <info>Welcome to Wandu Framework!</info>');
        
        $basePath = $this->getPasePath();
        if (file_exists($basePath . '/.wandu.php')) {
            throw new \RuntimeException('already installed. if you want to re-install, remove the ".wandu.php" file!');
        }
        
        $appNamespace = $this->getAppNamespace();
    }
    
    protected function getAppNamespace()
    {
        return $this->io->ask('app namespace?', 'Wandu\\App', function ($namespace) {
            return rtrim($namespace, '\\');
        });
    }

    protected function getPasePath()
    {
        $default = getcwd();
        return $this->io->ask('install path?', $default, function ($path) use ($default) {
            if ($path[0] === '~') {
                if (!function_exists('posix_getuid')) {
                    throw new \InvalidArgumentException('cannot use tilde(~) character in your php enviroment.');
                }
                $info = posix_getpwuid(posix_getuid());
                $path = str_replace('~', $info['dir'], $path);
            }
            if ($path[0] !== '/') {
                $path = "{$default}/{$path}";
            }
            return rtrim($path, '/');
        });
    }
}
