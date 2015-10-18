<?php
namespace Wandu\DI\Helper;

class PathLoader
{
    /** @var string */
    protected $base;

    /**
     * @param string $base
     */
    public function __construct($base)
    {
        $this->base = $base;
    }

    /**
     * @param string $path
     * @return string
     */
    public function handle($path)
    {
        return $this->base .'/' .$path;
    }
}
