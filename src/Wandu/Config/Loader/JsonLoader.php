<?php
namespace Wandu\Config\Loader;

use Wandu\Config\Contracts\Loader;

class JsonLoader implements Loader
{
    /** @var string */
    protected $pattern;

    public function __construct(string $pattern = '~^[a-z_][a-z0-9_]*\.json$~')
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
        return json_decode(file_get_contents($path), true);
    }
}
