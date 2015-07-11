<?php
namespace Wandu\DI\Stub;

class StubClient
{
    /**
     * @param DepInterface $depInterface
     * @return static
     */
    public static function create(DepInterface $depInterface)
    {
        return new static($depInterface);
    }

    /** @var DepInterface */
    protected $dependency;

    /**
     * @param DepInterface $dependency
     * @param array $config
     */
    public function __construct(DepInterface $dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * @return DepInterface
     */
    public function getDependency()
    {
        return $this->dependency;
    }
}
