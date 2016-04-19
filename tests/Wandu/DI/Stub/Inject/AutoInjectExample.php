<?php
namespace Wandu\DI\Stub\Inject;

class AutoInjectExample
{
    /**
     * @Autowired
     * @var \Wandu\DI\Stub\Resolve\DependInterface
     */
    private $something;

    /** @var mixed */
    private $otherthing;

    /**
     * @return mixed
     */
    public function getSomething()
    {
        return $this->something;
    }

    public function getOtherthing()
    {
        return $this->otherthing;
    }
}
