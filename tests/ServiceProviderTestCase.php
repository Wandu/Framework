<?php
namespace Wandu;

use PHPUnit_Framework_TestCase;
use ReflectionObject;
use Wandu\Config\Config;
use Wandu\Foundation\Application;
use Wandu\Foundation\Kernels\NullKernel;

abstract class ServiceProviderTestCase extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Foundation\Application */
    protected $app;
    
    /** @var string */
    protected $basePath;
    
    /** @var array */
    protected $config = [];

    /**
     * @return \Wandu\DI\ServiceProviderInterface
     */
    abstract public function getServiceProvider();

    /**
     * @return array
     */
    abstract public function getRegisterClasses();

    public function setUp()
    {
        $this->app = new Application(new NullKernel());
        $this->app['base_path'] = $this->basePath;
        $this->app['config'] = new Config($this->config);
    }

    public function testCheckRegisteredClasses()
    {
        $refl = new ReflectionObject($this->app);
        $propertyRefl = $refl->getProperty('containees');
        $propertyRefl->setAccessible(true);
        $containees = $propertyRefl->getValue($this->app);
        foreach ($this->getRegisterClasses() as $name => $class) {
            if (is_int($name)) {
                $name = $class;
            }
            static::assertFalse(isset($containees[$name]), "error in check registered classes. already exist \"{$name}\".");
        }

        $this->runRegister();
        $this->runBoot();

        $refl = new ReflectionObject($this->app);
        $propertyRefl = $refl->getProperty('containees');
        $propertyRefl->setAccessible(true);
        $containees = $propertyRefl->getValue($this->app);
        foreach ($this->getRegisterClasses() as $name => $class) {
            if (is_int($name)) {
                $name = $class;
            }
            static::assertTrue(isset($containees[$name]), "error in check registered classes. not exist \"{$name}\".");
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

    public function runRegister()
    {
        $this->app->register($this->getServiceProvider());
    }

    public function runBoot()
    {
        $this->app->boot();
    }
}
