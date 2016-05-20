<?php
namespace Wandu\Foundation;

use Wandu\DI\Container;

class Application extends Container
{
    const NAME = "Wandu";
    const VERSION = "3.0.0-alpha3";

    /** @var \Wandu\Foundation\KernelInterface */
    protected $kernel;

    /** @var \Wandu\Foundation\Application */
    public static $app;

    /**
     * @param \Wandu\Foundation\KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
        $this->instance(KernelInterface::class, $this->kernel = $kernel);
        $this->alias('kernel', KernelInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->kernel->boot($this);
        $this->setAsGlobal();
        parent::boot();
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

    /**
     * @return \Wandu\Foundation\Application
     */
    public function setAsGlobal()
    {
        $oldApp = static::$app;
        static::$app = $this;
        return $oldApp;
    }
}
