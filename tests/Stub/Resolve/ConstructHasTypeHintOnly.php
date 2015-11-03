<?php
namespace Wandu\DI\Stub\Resolve;

use Wandu\DI\Stub\RequiredLibraryInterface;

class ConstructHasTypeHintOnly
{
    /** @var \Wandu\DI\Stub\RequiredLibraryInterface */
    protected $requireLibrary;

    /**
     * @param \Wandu\DI\Stub\RequiredLibraryInterface $requiredLibrary
     */
    public function __construct(RequiredLibraryInterface $requiredLibrary)
    {
        $this->requireLibrary = $requiredLibrary;
    }

    /**
     * @return \Wandu\DI\Stub\RequiredLibraryInterface
     */
    public function getRequiredLibrary()
    {
        return $this->requireLibrary;
    }
}
