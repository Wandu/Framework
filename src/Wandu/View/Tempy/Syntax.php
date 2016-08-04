<?php
namespace Wandu\View\Tempy;

abstract class Syntax
{
    /** @var string */
    protected $syntaxOpen;

    /** @var array */
    protected $syntaxMiddles;

    /** @var string */
    protected $syntaxClose;

    /**
     * @param array $arguments
     * @param array $namespaces
     * @return string
     */
    abstract public function open(array $arguments, array $namespaces = []);

    /**
     * @param array $arguments
     * @param array $namespace
     * @return string
     */
    abstract public function close(array $arguments, array $namespace = []);

    /**
     * @param int $index
     * @param array $arguments
     * @param array $namespace
     * @return string
     */
    abstract public function middle($index, array $arguments, array $namespace = []);
}