<?php
namespace Wandu\DI\Stub\Resolve;

use Wandu\DI\Stub\RequiredLibraryInterface;

class ConstructHasTypeHintAndConfig
{
    /** @var \Wandu\DI\Stub\RequiredLibraryInterface */
    protected $requireLibrary;

    /** @var array */
    protected $configs;

    /**
     * @param \Wandu\DI\Stub\RequiredLibraryInterface $requiredLibrary
     * @param array $configs
     */
    public function __construct(RequiredLibraryInterface $requiredLibrary, array $configs)
    {
        $this->requireLibrary = $requiredLibrary;
        $this->configs = $configs;
    }

    /**
     * @return \Wandu\DI\Stub\RequiredLibraryInterface
     */
    public function getRequiredLibrary()
    {
        return $this->requireLibrary;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }
}
