<?php
namespace Wandu\DI\Stub;

class HttpController
{
    /**
     * @param \Wandu\DI\Stub\Renderable $depInterface
     * @return static
     */
    public static function create(Renderable $depInterface)
    {
        return new static($depInterface);
    }

    /** @var \Wandu\DI\Stub\Renderable */
    protected $renderer;

    /**
     * @param \Wandu\DI\Stub\Renderable $renderer
     */
    public function __construct(Renderable $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return \Wandu\DI\Stub\Renderable
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    public function callWithRenderer(Renderable $dep)
    {
        return 'call with renderer';
    }
}
