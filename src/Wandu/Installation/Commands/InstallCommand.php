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
        
        $basePath = $this->getBasePath();
        $composerFile = $this->getComposerFilePath($basePath);

        if (file_exists($basePath . '/.wandu.php')) {
            throw new \RuntimeException('already installed. if you want to re-install, remove the ".wandu.php" file!');
        }
        
        $appNamespace = $this->getAppNamespace();

        // copy files!
        $this->copyBaseFiles($basePath);
        
        // set composer
        $this->saveAutoloadToComposer($appNamespace, $composerFile);

        $this->output->writeln("<info>Install Complete!</info>");
    }
    
    protected function copyBaseFiles($basePath)
    {
        $this->output->writeln("copy files... ");

        $directories = [
            '/public',
            '/views',
            '/app',
            '/migrations',
            '/cache',
            '/cache/views',
        ];
        foreach ($directories as $directory) {
            if (!is_dir($basePath . $directory)) {
                mkdir($basePath . $directory);
                $this->output->writeln(" - create directory {$directory}");
            } else {
                $this->output->writeln(" - already exists {$directory}");
            }
        }

        $skeletonbDir = dirname(__DIR__) . '/skeleton';

        /** @var \SplFileInfo $file */
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($skeletonbDir));
        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            $target = str_replace($skeletonbDir, '', $file->getRealPath());
            $this->output->writeln(" - create file {$target}");
            copy($file->getRealPath(), $basePath . $target);
        }

        $this->output->writeln("<info>ok</info>");
    }
    
    protected function saveAutoloadToComposer($appNamespace, $composerFile)
    {
        $this->output->write("save autoload setting to composer... ");

        $composerJson = [];
        if (file_exists($composerFile)) {
            $composerJson = json_decode(file_get_contents($composerFile), true);
            if (json_last_error()) {
                $composerJson = [];
            }
        }
        
        if (!isset($composerJson['autoload'])) {
            $composerJson['autoload'] = [];
        }
        if (!isset($composerJson['autoload']['psr-4'])) {
            $composerJson['autoload']['psr-4'] = [];
        }
        $composerJson['autoload']['psr-4'][$appNamespace . '\\'] = 'app/';
        file_put_contents(
            $composerFile,
            json_encode($composerJson, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) . "\n"
        );
        $this->output->writeln("<info>ok</info>");
    }
    
    protected function getAppNamespace()
    {
        return $this->io->ask('app namespace?', 'Wandu\\App', function ($namespace) {
            return rtrim($namespace, '\\');
        });
    }

    protected function getBasePath()
    {
        return $this->io->ask('install path?', getcwd(), function ($path) {
            if ($path[0] === '~') {
                if (!function_exists('posix_getuid')) {
                    throw new \InvalidArgumentException('cannot use tilde(~) character in your php enviroment.');
                }
                $info = posix_getpwuid(posix_getuid());
                $path = str_replace('~', $info['dir'], $path);
            }
            if ($path[0] !== '/') {
                $path = getcwd() . "/{$path}";
            }
            return rtrim($path, '/');
        });
    }

    protected function getComposerFilePath($basePath)
    {
        $composerFile = $basePath . '/composer.json';
        if (!file_exists($composerFile)) {
            $composerFile = $this->io->ask('composer path?', $basePath . '/composer.json', function ($path) {
                if ($path[0] === '~') {
                    if (!function_exists('posix_getuid')) {
                        throw new \InvalidArgumentException('cannot use tilde(~) character in your php enviroment.');
                    }
                    $info = posix_getpwuid(posix_getuid());
                    $path = str_replace('~', $info['dir'], $path);
                }
                if ($path[0] !== '/') {
                    $path = getcwd() . "/{$path}";
                }
                return rtrim($path, '/');
            });
        }
        return $composerFile;
    }
}
