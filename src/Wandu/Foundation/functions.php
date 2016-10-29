<?php
namespace Wandu\Foundation {
    /**
     * @return \Wandu\Foundation\Application
     */
    function app()
    {
        return Application::$app;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    function config($name, $default = null)
    {
        return app()->get('config')->get($name, $default);
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
        return app()->get('base_path') . '/' . $path;
    }
}

namespace Wandu\View {

    use Wandu\Foundation\Application;
    use Wandu\View\Contracts\RenderInterface;

    /**
     * @param string $template
     * @param array $attributes
     * @param string $basePath
     * @return string
     */
    function render($template, array $attributes = [], $basePath = null)
    {
        return Application::$app[RenderInterface::class]->render($template, $attributes, $basePath);
    }
}
