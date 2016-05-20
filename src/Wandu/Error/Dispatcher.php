<?php
namespace Wandu\Error;

use ErrorException;
use SplQueue;
use Throwable;

/**
 * @reference https://github.com/filp/whoops
 */
class Dispatcher
{
    /** @var bool */
    protected $canThrowExceptions = true;

    /** @var \SplQueue */
    protected $handlers;
    
    public function __construct()
    {
        $this->handlers = new SplQueue();
    }

    public function boot()
    {
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * @param \Wandu\Error\HandlerInterface $handler
     * @return \Wandu\Error\Dispatcher
     */
    public function pushHandler(HandlerInterface $handler)
    {
        $this->handlers->enqueue($handler);
        return $this;
    }

    /**
     * @param \Throwable $exception
     * @return string
     */
    public function handleException(Throwable $exception)
    {
        while (!$this->handlers->isEmpty()) {
            /* @var \Wandu\Error\HandlerInterface $handler */
            $handler = $this->handlers->dequeue();
            $return = $handler->handle($exception);
            if ($return === HandlerInterface::RETURN_EXIT) {
                break;
            }
        }
        
        $output = ob_get_contents();
        while (ob_get_level()) {
            ob_end_clean();
        }
        return $output;
    }

    /**
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @return bool
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = null, $line = null)
    {
        if ($level & error_reporting()) {
            $exception = new ErrorException($message, $level, $level, $file, $line);
            if ($this->canThrowExceptions) {
                throw $exception;
            } else {
                $this->handleException($exception);
            }
            // Do not propagate errors which were already handled by Whoops.
            return true;
        }

        // Propagate error to the next handler, allows error_get_last() to
        // work on silenced errors.
        return false;
    }

    public function handleShutdown()
    {
        // If we reached this step, we are in shutdown handler.
        // An exception thrown in a shutdown handler will not be propagated
        // to the exception handler. Pass that information along.
        $this->canThrowExceptions = false;

        $error = error_get_last();
        if ($error && $this->isLevelFatal($error['type'])) {
            // If there was a fatal error,
            // it was not handled in handleError yet.
            $this->handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }

    private static function isLevelFatal($level)
    {
        $errors = E_ERROR;
        $errors |= E_PARSE;
        $errors |= E_CORE_ERROR;
        $errors |= E_CORE_WARNING;
        $errors |= E_COMPILE_ERROR;
        $errors |= E_COMPILE_WARNING;
        return ($level & $errors) > 0;
    }
}
