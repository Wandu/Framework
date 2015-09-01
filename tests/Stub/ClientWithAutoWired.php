<?php
namespace Wandu\DI\Stub;

class ClientWithAutoWired
{
    /**
     * @Autowired
     * @var \Wandu\DI\Stub\DepInterface
     */
    private $dep;

    /**
     * @return \Wandu\DI\Stub\DepInterface
     */
    public function getDep()
    {
        return $this->dep;
    }
}
