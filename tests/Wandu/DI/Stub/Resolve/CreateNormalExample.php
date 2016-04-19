<?php
namespace Wandu\DI\Stub\Resolve;

class CreateNormalExample
{
    /** @var \Wandu\DI\Stub\Resolve\DependInterface */
    protected $depend;

    /**
     * @param \Wandu\DI\Stub\Resolve\DependInterface $depend
     */
    public function __construct(DependInterface $depend)
    {
        $this->depend = $depend;
    }

    /**
     * @return \Wandu\DI\Stub\Resolve\DependInterface
     */
    public function getDepend()
    {
        return $this->depend;
    }
}
