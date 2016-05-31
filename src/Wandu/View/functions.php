<?php
namespace Wandu\View {

    use Wandu\Foundation\Application;
    use Wandu\View\Contracts\RenderInterface;

    /**
     * @param string $template
     * @param array $attributes
     * @return string
     */
    function render($template, array $attributes = [])
    {
        return Application::$app[RenderInterface::class]->render($template, $attributes);
    }
}
