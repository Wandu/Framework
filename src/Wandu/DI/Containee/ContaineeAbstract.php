<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContaineeInterface;

abstract class ContaineeAbstract implements ContaineeInterface
{
    /** @var bool */
    protected $frozen = false;

    /**
     * @return bool
     */
    public function isFrozen()
    {
        return $this->frozen;
    }
}
