<?php
namespace Wandu\DI\Stub;

class AutoWiredClient
{
    /**
     * @Autowired
     * @var \Wandu\DI\Stub\DepInterface
     */
    private $dep1;

    /**
     * @Autowired
     * @var DepInterface
     */
    private $dep2;

    /**
     * @return DepInterface
     */
    public function getDep1()
    {
        return $this->dep1;
    }

    /**
     * @return DepInterface
     */
    public function getDep2()
    {
        return $this->dep2;
    }
}
