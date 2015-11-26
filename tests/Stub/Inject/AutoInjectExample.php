<?php
namespace Wandu\DI\Stub\Inject;

class AutoInjectExample
{
    /**
     * @Autowired
     * @var \Wandu\DI\Stub\Resolve\DependInterface
     */
    private $requiredLibrary;

    /**
     * @return \Wandu\DI\Stub\Resolve\DependInterface
     */
    public function getRequiredLibrary()
    {
        return $this->requiredLibrary;
    }
}
