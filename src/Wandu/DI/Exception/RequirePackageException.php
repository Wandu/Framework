<?php
namespace Wandu\DI\Exception;

use Interop\Container\Exception\NotFoundException;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class RequirePackageException extends RuntimeException implements NotFoundExceptionInterface, NotFoundException
{
    /** @var string */
    protected $class;

    /** @var string */
    protected $package;

    /**
     * @param string $class
     * @param string $package
     */
    public function __construct($class, $package)
    {
        $traces = debug_backtrace();
        $this->message = "cannot find the \"{$class}\" class, install \"{$package}\" package.";
        $this->package = $package;
        if (isset($traces[1])) {
            $this->file = $traces[1]['file'];
            $this->line = $traces[1]['line'];
        }
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }
}
