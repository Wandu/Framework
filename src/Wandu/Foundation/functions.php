<?php
namespace Wandu\Foundation
{
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
     * @param string $path
     * @return string
     */
    function path($path)
    {
        return app()->get('base_path') . '/' . $path;
    }
}
