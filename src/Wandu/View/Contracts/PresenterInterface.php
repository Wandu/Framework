<?php
namespace Wandu\View\Contacts;

interface PresenterInterface
{
    /**
     * @param string $template
     * @param array $values
     * @return string
     */
    public function render($template, array $values = []);
}
