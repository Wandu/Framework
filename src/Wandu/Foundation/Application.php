<?php
namespace Wandu\Foundation;

use Wandu\DI\Container;
use Wandu\Foundation\Contracts\KernelInterface;

class Application extends Container
{
    const NAME = "Wandu";
    const VERSION = "4.0-dev";

    /** @var \Wandu\Foundation\Contracts\KernelInterface */
    protected $kernel;

    /**
     * @param \Wandu\Foundation\Contracts\KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
        $this->instance(KernelInterface::class, $this->kernel = $kernel);
        $this->alias('kernel', KernelInterface::class);
        $this->setAsGlobal();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (!$this->isBooted) {
            $this->kernel->boot($this);
            parent::boot();
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $this->boot();
        return $this->kernel->execute($this);
    }
}
