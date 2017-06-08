<?php
namespace Wandu\Config\Loader;

use InvalidArgumentException;
use Wandu\Config\Contracts\Loader;

abstract class LoaderAbstract implements Loader
{
    /** @var string */
    protected $fileName;

    public function __construct(string $fileName)
    {
        if (!file_exists($fileName)) {
            throw new InvalidArgumentException("\"{$fileName}\" is not file name.");
        }
        $this->fileName = $fileName;
    }
}
