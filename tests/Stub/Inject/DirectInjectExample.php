<?php
namespace Wandu\DI\Stub\Inject;

class DirectInjectExample
{
    /** @var mixed */
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
}
