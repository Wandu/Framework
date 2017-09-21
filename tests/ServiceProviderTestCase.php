<?php
namespace Wandu;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Wandu\Config\Config;
use Wandu\Config\Contracts\Config as ConfigContract;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Foundation\Application;
use Wandu\Foundation\Contracts\Bootstrap;

abstract class ServiceProviderTestCase extends TestCase 
{
    /** @var \Wandu\Foundation\Application */
    protected $app;
    
    /** @var string */
    protected $basePath;
    
    /** @var array */
    protected $config = [];

    abstract public function getServiceProvider(): ServiceProviderInterface;
    abstract public function getRegisterClasses(): array;
    
    public function setUp()
    {
        $this->app = new Application(new class implements Bootstrap {
            public function providers(): array { return []; }
            public function boot(ContainerInterface $app) {}
            public function execute(ContainerInterface $app): int { return 0; }
        });
        $this->app->instance(ConfigContract::class, new Config($this->config));
    }

    public function testCheckRegisteredClasses()
    {
        $refl = new ReflectionObject($this->app);
        $propertyRefl = $refl->getProperty('descriptors');
        $propertyRefl->setAccessible(true);
        $descriptors = $propertyRefl->getValue($this->app);
        foreach ($this->getRegisterClasses() as $name => $class) {
            if (is_int($name)) {
                $name = $class;
            }
            static::assertFalse(isset($descriptors[$name]), "error in check registered classes. already exist \"{$name}\".");
        }

        $this->runRegister();
        $this->runBoot();

        $refl = new ReflectionObject($this->app);
        $propertyRefl = $refl->getProperty('descriptors');
        $propertyRefl->setAccessible(true);
        $descriptors = $propertyRefl->getValue($this->app);
        foreach ($this->getRegisterClasses() as $name => $class) {
            if (is_int($name)) {
                $name = $class;
            }
            static::assertTrue(isset($descriptors[$name]), "error in check registered classes. not exist \"{$name}\".");
        }
    }

    public function testCallRegisteredClasses()
    {
        $this->runRegister();
        $this->runBoot();

        foreach ($this->getRegisterClasses() as $name => $class) {
            if (is_int($name)) {
                $name = $class;
            }
            static::assertInstanceOf(
                $class,
                $this->app->get($name),
                "error in call registered classes. \"{$name}\" is not instance of \"{$class}\"."
            );
        }
    }

    private function runRegister()
    {
        $this->app->register($this->getServiceProvider());
    }

    private function runBoot()
    {
        $this->app->boot();
    }
}
