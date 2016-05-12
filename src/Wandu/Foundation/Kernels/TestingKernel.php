<?php
namespace Wandu\Foundation\Kernels;

use Wandu\Foundation\KernelInterface;
use Wandu\DI\ContainerInterface;
use PHPUnit_Framework_Test;
use Wandu\DI\Exception\CannotInjectException;

class TestingKernel implements KernelInterface
{
    /** @var \PHPUnit_Framework_Test */
    protected $test;

    public function __construct(PHPUnit_Framework_Test $test)
    {
        $this->test = $test;
    }

    public function boot(ContainerInterface $app)
    {
    }

    public function execute(ContainerInterface $app)
    {
        try {
            $app->inject($this->test);
        } catch (CannotInjectException $exception) {
            $propertyName = $exception->getProperty();
            $className = $exception->getClass();
            if (class_exists($className)) {
                $app->inject($this->test, [
                    $propertyName => $app->create($className)
                ]);
            } else {
                throw $exception;
            }
        }
    }

    public function result()
    {
        return 0;
    }
}
