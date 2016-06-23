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
     * @param string $path
     * @return string
     */
    function path($path)
    {
        return app()->get('base_path') . '/' . $path;
    }
}
