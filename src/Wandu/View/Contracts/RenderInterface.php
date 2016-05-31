<?php
namespace Wandu\View\Contracts;

interface RenderInterface
{
    /**
     * @param array $values
     * @return \Wandu\View\Contracts\RenderInterface
     */
    public function with(array $values = []);

    /**
     * @param string $template
     * @param array $attributes
     * @return string
     */
    public function render($template, array $attributes = []);
}
