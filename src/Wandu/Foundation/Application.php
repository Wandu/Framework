<?php
namespace Wandu\Foundation;

use Wandu\DI\Container;
use Wandu\Foundation\Contracts\Bootstrap;

class Application extends Container
{
    const NAME = "Wandu";
    const VERSION = "4.0-dev";

    /** @var \Wandu\Foundation\Contracts\Bootstrap */
    protected $bootstrapper;
    
    public function __construct(Bootstrap $bootstrapper)
    {
        parent::__construct();
        $this->bootstrapper = $bootstrapper;
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
