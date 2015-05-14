<?php
namespace Jicjjang\June;

use Psr\Http\Message\RequestInterface as Base;

interface RequestInterface extends Base
{
    /**
     * @param array $arguments
     * @return self
     */
    public function setArguments(array $arguments);

    /**
     * @return array
     */
    public function getArguments();

    /**
     * @param string $name
     * @param mixed $argument
     * @return self
     */
    public function setArgument($name, $argument);

    /**
     * @param string $name
     * @return mixed
     */
    public function getArgument($name);
}
