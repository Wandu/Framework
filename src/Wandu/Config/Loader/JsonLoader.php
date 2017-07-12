<?php
namespace Wandu\Config\Loader;

use Wandu\Config\Contracts\Loader;

class JsonLoader implements Loader
{
    /**
     * {@inheritdoc}
     */
    public function test(string $path): bool
    {
        return substr($path, -5) === '.json' && file_exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $path)
    {
        return json_decode(file_get_contents($path), true);
    }
}
