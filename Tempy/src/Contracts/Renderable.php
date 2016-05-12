<?php
namespace Wandu\Tempy\Contracts;

interface Renderable
{
    /**
     * @param string $template
     * @param array $values
     * @return string
     */
    public function render($template, array $values = []);
}
