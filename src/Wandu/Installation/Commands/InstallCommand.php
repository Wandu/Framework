<?php
namespace Wandu\Installation\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use Wandu\Console\Command;
use Wandu\Installation\SkeletonBuilder;

class InstallCommand extends Command
{
    /** @var string */
    protected $description = "Install <comment>Wandu Framework</comment> to your project directory.";

    /** @var \Symfony\Component\Console\Style\SymfonyStyle */
    protected $io;
    
    /** @var array */
    protected $options = [
        'namespace?' => 'default wandu app namespace',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function withIO(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        return parent::withIO($input, $output);
    }

    public function execute()
    {
        if (file_exists('.wandu.php')) {
            throw new \RuntimeException('already installed. if you want to re-install, remove the ".wandu.php" file!');
        }

        $this->output->writeln('Hello, <info>Welcome to Wandu Framework!</info>');

        $appBasePath = getcwd(); //static::filterPath($this->askAppBasePath('install path?', $basePath));

        $appNamespace = $this->input->hasOption('namespace') ?
            $this->input->getOption('namespace') :
            $this->askAppNamespace('app namespace?', 'Wandu\\App');
        
        $this->install($appBasePath, $appNamespace);

        // set composer
        $this->saveAutoloadToComposer($appNamespace);

        // run composer
        $this->runDumpAutoload();

        $this->output->writeln("<info>Install Complete!</info>");
    }

    protected function install($appBasePath, $appNamespace)
    {
        $installer = new SkeletonBuilder($appBasePath, __DIR__ . '/../skeleton');
        $replacers = [
            'WanduSkeleton' => $appNamespace,
        ];
        $installer->build($replacers);

        file_put_contents($appBasePath . '/.wandu.php', <<<PHP
<?php

define('WANDU_DB_HOST', 'localhost');
define('WANDU_DB_DBNAME', 'wandu');
define('WANDU_DB_USERNAME', 'root');
define('WANDU_DB_PASSWORD', 'root');
define('WANDU_DB_PREFIX', 'local_');

return new {$appNamespace}\ApplicationDefinition();

PHP
        );
    }
    
    protected function runDumpAutoload()
    {
        if (file_exists('composer.phar')) {
            $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));
            $composer = "{$binary} composer.phar";
        } else {
            $composer = 'composer';
        }
        (new Process("{$composer} dump-autoload", getcwd()))->run();
    }

    protected function saveAutoloadToComposer($appNamespace)
    {
        $this->output->write("save autoload setting to composer... ");

        $composerJson = [];
        if (file_exists('composer.json')) {
            $composerJson = json_decode(file_get_contents('composer.json'), true);
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
        $composerJson['autoload']['psr-4'][$appNamespace . '\\'] = 'src/';
        file_put_contents(
            'composer.json',
            json_encode($composerJson, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) . "\n"
        );
        $this->output->writeln("<info>ok</info>");
    }

    protected function askAppNamespace($message, $default)
    {
        return $this->io->ask($message, $default, function ($namespace) {
            return rtrim($namespace, '\\');
        });
    }

    /**
     * @param string $message
     * @param string $default
     * @return string
     */
    protected function askAppBasePath($message, $default)
    {
        if (function_exists('posix_getuid')) {
            $info = posix_getpwuid(posix_getuid());
            $default = str_replace($info['dir'], '~', $default);
        }
        return $this->io->ask($message, $default);
    }

    /**
     * @param string $path
     * @return string
     */
    static protected function filterPath($path)
    {
        if ($path[0] === '~') {
            if (!function_exists('posix_getuid')) {
                throw new \InvalidArgumentException('cannot use tilde(~) character in your php environment.');
            }
            $info = posix_getpwuid(posix_getuid());
            $path = str_replace('~', $info['dir'], $path);
        }

        $basePath = getcwd();
        if ($path === $basePath) {
            $path = '.';
        } elseif (strpos($path, $basePath) === 0) {
            $path = str_replace("{$basePath}/", './', $path);
        } elseif ($path[0] === '/' || strpos($path, './') === 0) {
        } elseif (strpos($path, '..') === 0) {
            $path = realpath($basePath . '/' . $path);
        } else {
            $path = './' . $path;
        }
        return rtrim($path, '/');
    }
}
