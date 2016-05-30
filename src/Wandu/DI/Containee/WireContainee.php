<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class WireContainee extends BindContainee
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $this->frozen = true;
        if (!isset($this->caching)) {
            $this->caching = $this->container->create($this->className);
            $this->container->inject($this->caching);
        }
        return $this->caching;
    }
}
