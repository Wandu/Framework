<?php
namespace Wandu\Config\Loader;

use Symfony\Component\Yaml\Yaml;
use Wandu\Config\Contracts\Loader;

class YmlLoader implements Loader
{
    /** @var string */
    protected $pattern;

    public function __construct(string $pattern = '~^[a-z_][a-z0-9_]*\.yml$~')
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function test(string $path): bool
    {
        return file_exists($path) && preg_match($this->pattern, pathinfo($path)['basename']);
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $path)
    {
        return Yaml::parse(file_get_contents($path));
    }
}
