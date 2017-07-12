<?php
namespace Wandu\Config\Loader;

use M1\Env\Parser;
use Wandu\Config\Contracts\Loader;

class EnvLoader implements Loader
{
    /**
     * {@inheritdoc}
     */
    public function test(string $path): bool
    {
        return substr($path, -4) === '.env' && file_exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $path)
    {
        return (new Parser(file_get_contents($path)))->getContent();
    }
}
