<?php
namespace Wandu\Tempy;

use Wandu\Tempy\Contracts\Renderable;

class Renderer implements Renderable
{
    /** @var string */
    protected $path;

    /** @var array */
    protected $config;

    /**
     * @param string $path
     * @param array $config
     */
    public function __construct($path, array $config = [])
    {
        $this->path = $path;
        $this->config = $config + [
                'cache_enabled' => false,
                'cache_directory' => $path . '/cache',
            ];
    }

    public function render($name, array $values = [])
    {

    }
}
