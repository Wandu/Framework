<?php
namespace Wandu\Foundation;

use Wandu\DI\Container;
use Wandu\Foundation\Contracts\Bootstrapper;

class Application extends Container
{
    const NAME = "Wandu";
    const VERSION = "4.0-dev";

    /** @var \Wandu\Foundation\Contracts\Bootstrapper */
    protected $bootstrapper;
    
    public function __construct(Bootstrapper $bootstrapper)
    {
        parent::__construct();
        $this->instance(Bootstrapper::class, $this->bootstrapper = $bootstrapper);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (!$this->isBooted) {
            foreach ($this->bootstrapper->providers() as $provider) {
                $this->register($provider);
            }
            $this->bootstrapper->boot($this);
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
        return $this->bootstrapper->execute($this);
    }
}
