<?php
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
