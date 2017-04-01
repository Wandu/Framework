<?php
namespace Wandu\View\Phiew\Contracts;

use Wandu\View\Phiew\Template;

interface ResolverInterface
{
    /**
     * @param string $name
     * @return \Wandu\View\Phiew\Template
     */
    public function resolve(string $name): Template;
}
