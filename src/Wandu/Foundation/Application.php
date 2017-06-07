<?php
namespace Wandu\Foundation;

use Wandu\DI\Container;
use Wandu\Foundation\Contracts\Definition;
use Wandu\Foundation\Contracts\Bootstrapper;

class Application extends Container
{
    const NAME = "Wandu";
    const VERSION = "4.0-dev";

    /** @var \Wandu\Foundation\Contracts\Bootstrapper */
    protected $bootstrapper;
    
    /** @var \Wandu\Foundation\Contracts\Definition */
    protected $definition;

    public function __construct(Bootstrapper $bootstrapper, Definition $definition)
    {
        parent::__construct();
        $this->instance(Bootstrapper::class, $this->bootstrapper = $bootstrapper);
        $this->instance(Definition::class, $this->definition = $definition);
        $this->setAsGlobal();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (!$this->isBooted) {
            $this->bootstrapper->boot($this, $this->definition);
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
        return $this->bootstrapper->execute($this, $this->definition);
    }
}
