<?php
namespace Wandu\DI\Stub\Inject;

class AutoInjectExample
{
    /**
     * @Autowired
     * @var \Wandu\DI\Stub\RequiredLibraryInterface
     */
    private $requiredLibrary;

    /**
     * @return \Wandu\DI\Stub\RequiredLibraryInterface
     */
    public function getRequiredLibrary()
    {
        return $this->requiredLibrary;
    }
}
