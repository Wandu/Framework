<?php
namespace Wandu\DI\Stub;

class HttpControllerWithConfig
{
    /** @var \Wandu\DI\Stub\Renderable */
    protected $renderer;

    /** @var array */
    protected $config;

    /**
     * @param \Wandu\DI\Stub\Renderable $renderer
     * @param array $config
     */
    public function __construct(Renderable $renderer, array $config)
    {
        $this->renderer = $renderer;
        $this->config = $config;
    }

    /**
     * @return \Wandu\DI\Stub\Renderable
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
