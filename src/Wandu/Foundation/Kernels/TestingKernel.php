<?php
namespace Wandu\Foundation\Kernels;

use PHPUnit_Framework_Test;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Exception\CannotInjectException;
use Wandu\Foundation\Contracts\DefinitionInterface;

class TestingKernel extends KernelAbstract
{
    /** @var \PHPUnit_Framework_Test */
    protected $test;

    public function __construct(DefinitionInterface $definition, PHPUnit_Framework_Test $test)
    {
        parent::__construct($definition);
        $this->test = $test;
    }
    
    public function execute(ContainerInterface $app)
    {
        $this->useErrorHandling();
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
}
