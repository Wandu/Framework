<?php
namespace Wandu\Config\Loader;

use M1\Env\Parser;
use Wandu\Config\Contracts\Loader;

class EnvLoader implements Loader
{
    /** @var string */
    protected $pattern;

    public function __construct(string $pattern = '~^[a-z_][a-z0-9_]*\.env$~')
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
        return (new Parser(file_get_contents($path)))->getContent();
    }
}
