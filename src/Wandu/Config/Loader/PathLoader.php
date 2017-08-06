<?php
namespace Wandu\Config\Loader;

use DirectoryIterator;
use Wandu\Config\Contracts\Loader;

class PathLoader implements Loader
{
    /** @var \Wandu\Config\Contracts\Loader[] */
    protected $loaders;
    
    /** @var string */
    protected $pattern;
    
    public function __construct(array $loaders = [], string $pattern = '~^[a-z_][a-z0-9_]*$~')
    {
        $this->loaders = $loaders;
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function test(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $path)
    {
        $configToReturn = [];
        foreach (new DirectoryIterator($path) as $file) {
            $filename = $file->getFilename();
            if ($filename === '.' || $filename === '..') continue;
            if ($file->isFile()) {
                $name = $file->getBasename("." . $file->getExtension());
                foreach ($this->loaders as $loader) {
                    if ($loader->test($file->getRealPath())) {
                        if ($config = $loader->load($file->getRealPath())) {
                            $configToReturn[$name] = $config;
                        }
                    }
                }
            } else {
                if (preg_match($this->pattern, $filename)) {
                    $configToReturn[$filename] = $this->load($file->getRealPath());
                }
            }
        }
        return $configToReturn;
    }
}
