<?php
namespace Wandu\Foundation
{

    use function Wandu\DI\container;

    /**
     * @deprecated use function Wandu\DI\container
     * @return \Wandu\DI\ContainerInterface
     */
    function app()
    {
        return container();
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    function config($name, $default = null)
    {
        return container()->get('config')->get($name, $default);
    }

    /**
     * @param string|array $path
     * @return string|array
     */
    function path($path)
    {
        if (is_array($path)) {
            return array_map(function ($path) {
                return path($path);
            }, $path);
        }
        if (container()->has('base_path')) {
            return container()->get('base_path') . '/' . $path;
        }
        return $path;
    }
}

namespace Wandu\View
{
    use Wandu\View\Contracts\RenderInterface;
    use function Wandu\DI\container;

    /**
     * @param string $template
     * @param array $attributes
     * @param string $basePath
     * @return string
     */
    function render($template, array $attributes = [], $basePath = null)
    {
        return container()->get(RenderInterface::class)->render($template, $attributes, $basePath);
    }
}
