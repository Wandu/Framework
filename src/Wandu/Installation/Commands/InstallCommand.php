<?php
namespace Wandu\Installation\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use Wandu\Console\Command;
use Wandu\Console\Reader;
use Wandu\Installation\SkeletonBuilder;

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
        if (file_exists(WANDU_PATH . '/.wandu.php')) {
            throw new \RuntimeException('already installed. if you want to re-install, remove the ".wandu.php" file!');
        }

        $this->output->writeln('Hello, <info>Welcome to Wandu Framework!</info>');

        $composerFile = WANDU_PATH . '/composer.json';

        $appBasePath = $this->getAppBasePath();
        $appNamespace = $this->getAppNamespace();

        $installer = new SkeletonBuilder($appBasePath);
        $installer->install([
            'namespace' => $appNamespace,
            'path' => str_replace(WANDU_PATH, '', $appBasePath),
        ]);

        // set composer
        $this->saveAutoloadToComposer($appNamespace, $composerFile);

        // run composer
        $this->runDumpAutoload($composerFile);

        $this->output->writeln("<info>Install Complete!</info>");
    }

    protected function runDumpAutoload($composerFile)
    {
        $basePath = dirname($composerFile);
        if (file_exists($basePath . '/composer.phar')) {
            $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));
            $composer = "{$binary} composer.phar";
        } else {
            $composer = 'composer';
        }
        (new Process("{$composer} dump-autoload", $basePath))->run();
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

    /**
     * @return string
     */
    protected function getAppNamespace()
    {
        return $this->io->ask('app namespace?', 'Wandu\\App', function ($namespace) {
            return rtrim($namespace, '\\');
        });
    }

    /**
     * @return string
     */
    protected function getAppBasePath()
    {
        return $this->io->ask('install path?', WANDU_PATH, function ($path) {
            if ($path[0] === '~') {
                if (!function_exists('posix_getuid')) {
                    throw new \InvalidArgumentException('cannot use tilde(~) character in your php enviroment.');
                }
                $info = posix_getpwuid(posix_getuid());
                $path = str_replace('~', $info['dir'], $path);
            }
            if ($path[0] !== '/') {
                $path = WANDU_PATH . "/{$path}";
            }
            return rtrim($path, '/');
        });
    }
}