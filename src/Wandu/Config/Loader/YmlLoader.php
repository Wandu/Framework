<?php
namespace Wandu\Config\Loader;

use Symfony\Component\Yaml\Yaml;
use Wandu\Config\Contracts\Loader;

class YmlLoader implements Loader
{
    /**
     * {@inheritdoc}
     */
    public function test(string $path): bool
    {
        return substr($path, -4) === '.yml' && file_exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $path)
    {
        return Yaml::parse(file_get_contents($path));
    }
}
