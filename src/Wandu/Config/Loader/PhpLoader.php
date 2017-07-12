<?php
namespace Wandu\Config\Loader;

use Wandu\Config\Contracts\Loader;

class PhpLoader implements Loader
{
    /**
     * {@inheritdoc}
     */
    public function test(string $path): bool
    {
        return substr($path, -4) === '.php' && file_exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $path)
    {
        return require $path;
    }
}
