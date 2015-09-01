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
     * @return DepInterface
     */
    public function getDep()
    {
        return $this->dep;
    }
}
