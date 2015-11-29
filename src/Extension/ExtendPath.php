<?php
namespace Wandu\DI\Extension;

class ExtendPath
{
    /** @var string */
    protected $basePath;

    /**
     * @param string $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @param string $path
     * @return string
     */
    public function __invoke($path)
    {
        return $this->basePath . $path;
    }
}
