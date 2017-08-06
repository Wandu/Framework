<?php
namespace Wandu\Config\Loader;

use Wandu\Config\Contracts\Loader;
use Throwable;

class PhpLoader implements Loader
{
    /** @var string */
    protected $pattern;
    
    public function __construct(string $pattern = '~^[a-z_][a-z0-9_]*\.php$~')
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
        $level = ob_get_level();
        ob_start();
        try {
            $config = require $path;
            ob_end_flush();
            if (is_array($config)) {
                return $config;
            }
        } catch (Throwable $e) {
            while (ob_get_level() - $level > 0) ob_end_flush();
        }
        return [];
    }
}
