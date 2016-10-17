<?php
namespace Wandu\Foundation\Kernels;

use ErrorException;
use Wandu\Config\Config;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Foundation\Contracts\KernelInterface;

abstract class KernelAbstract implements KernelInterface
{
    /** @var \Wandu\DI\ContainerInterface */
    protected $app;

    /** @var \Wandu\Foundation\Contracts\DefinitionInterface */
    protected $definition;
    
    /** @var array */
    protected $attributes = [];

    /**
     * @param \Wandu\Foundation\Contracts\DefinitionInterface $definition
     */
    public function __construct(DefinitionInterface $definition)
    {
        $this->definition = $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
        $app->instance(Config::class, new Config($this->definition->configs()));
        $app->alias(ConfigInterface::class, Config::class);
        $app->alias('config', Config::class);
        foreach ($this->definition->providers() as $provider) {
            $app->register($app->create($provider));
        }
        $this->app = $app;
    }

    public function useErrorHandling()
    {
        if (version_compare(phpversion(), '7.0') < 0) {
            set_exception_handler([$this, 'handleException']);
            set_error_handler([$this, 'handleError']);
            register_shutdown_function([$this, 'handleShutdown']);
        }
    }

    /**
     * @param \Exception|\Throwable $exception
     * @return int
     */
    public function handleException($exception)
    {
        // .. do nothing ..
        return 0;
    }

    /**
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = null, $line = null)
    {
        if ($level & error_reporting()) {
            throw new ErrorException($message, $level, $level, $file, $line);
        }
    }

    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error && self::isLevelFatal($error['type'])) {
            $this->handleException(
                new ErrorException(
                    $error['message'],
                    $error['type'],
                    $error['type'],
                    $error['file'],
                    $error['line']
                )
            );
        }
    }

    protected static function isLevelFatal($level)
    {
        $errors = E_ERROR;
        $errors |= E_PARSE;
        $errors |= E_CORE_ERROR;
        $errors |= E_CORE_WARNING;
        $errors |= E_COMPILE_ERROR;
        $errors |= E_COMPILE_WARNING;
        return ($level & $errors) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}
