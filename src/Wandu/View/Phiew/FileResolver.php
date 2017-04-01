<?php
namespace Wandu\View\Phiew;

use Wandu\View\FileNotFoundException;
use Wandu\View\Phiew\Contracts\ResolverInterface;
use SplFileInfo;
use SplFileObject;

class FileResolver implements ResolverInterface 
{
    /** @var \Wandu\View\Phiew\Configuration */
    protected $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $name): Template
    {
        /** @var \SplFileInfo $path */
        foreach ($this->getPaths() as $path) {
            if (file_exists($templateFilePath = $path->getRealPath() . "/{$name}")) {
                return new Template(new SplFileObject($templateFilePath), $this);
            }
        }
        throw new FileNotFoundException("Cannot find the template file named '{$name}'.");
    }

    /**
     * @return \Generator
     */
    private function getPaths()
    {
        foreach ((array)($this->config->path) as $path) {
            if (is_string($path)) {
                $path = new SplFileInfo($path);
            }
            if (!$path instanceof SplFileInfo || !$path->isDir()) {
                continue;
            }
            yield $path;
        }
    }
}
