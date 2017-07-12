<?php
namespace Wandu\Config\Exception;

use RuntimeException;

class CannotLoadException extends RuntimeException
{
    /** @var string */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $message = "cannot load {$path}";
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
