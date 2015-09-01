<?php
namespace Wandu\DI\Stub;

class ClientWithBlankAutoWired
{
    /**
     * @Autowired
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
