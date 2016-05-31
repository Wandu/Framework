<?php
namespace Wandu\DI\Containee;

class WireContainee extends BindContainee
{
    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $this->frozen = true;
        if (!isset($this->caching)) {
            $this->caching = $this->container->create($this->className);
            $this->container->inject($this->caching);
        }
        return $this->caching;
    }
}
