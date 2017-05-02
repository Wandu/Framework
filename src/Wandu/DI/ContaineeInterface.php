<?php
namespace Wandu\DI;

interface ContaineeInterface
{
    /**
     * @return \Wandu\DI\ContaineeInterface
     */
    public function freeze();

    /**
     * @param array $attributes
     * @return \Wandu\DI\ContaineeInterface
     */
    public function assign(array $attributes = []);
    
    /**
     * @param bool $enabled
     * @return \Wandu\DI\ContaineeInterface
     */
    public function annotated($enabled = true);

    /**
     * @param bool $enabled
     * @return \Wandu\DI\ContaineeInterface
     */
    public function wire($enabled = true);
    
    /**
     * @param bool $enabled
     * @return \Wandu\DI\ContaineeInterface
     */
    public function factory($enabled = true);
}
