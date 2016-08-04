<?php
namespace Wandu\View\Tempy;

use Wandu\View\Contracts\RenderInterface;

class Renderer implements RenderInterface
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

    public function with(array $values = [])
    {
        // TODO: Implement with() method.
    }

    public function render($name, array $values = [], $basePath = null)
    {

    }
}
