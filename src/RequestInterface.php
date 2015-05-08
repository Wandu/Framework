<?php
namespace June;

use Psr\Http\Message\RequestInterface as BaseRequestInterface;

interface RequestInterface extends BaseRequestInterface
{
    /**
     * @param array $arguments
     * @return self
     */
    public function withArguments($arguments);

    /**
     * @return array
     */
    public function getArguments();

    /**
     * @param string $name
     * @param mixed $argument
     * @return self
     */
    public function withArgument($name, $argument);

    /**
     * @param string $name
     * @return mixed
     */
    public function getArgument($name);
}
