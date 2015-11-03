<?php
namespace Wandu\DI\Helper;

class ConfigLoader
{
    /** @var string */
    protected $path;

    /** @var array */
    protected $cachedConfig = [];

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function __invoke($name, $default = null)
    {
        if (!isset($this->cachedConfig[$name])) {
            $names = explode('.', $name);
            $fileName = array_shift($names);
            if (!isset($this->cachedConfig[$fileName])) {
                $path = "{$this->path}/{$fileName}.php";
                if (file_exists($path)) {
                    $this->cachedConfig[$fileName] = require $path;
                } else {
                    $this->cachedConfig[$fileName] = null;
                }
            }
            $dataToReturn = $this->cachedConfig[$fileName];
            while (isset($dataToReturn) && count($names)) {
                $name = array_shift($names);
                $dataToReturn = isset($dataToReturn[$name]) ? $dataToReturn[$name] : null;
            }
            $this->cachedConfig[$name] = $dataToReturn;
        }
        return isset($this->cachedConfig[$name]) ? $this->cachedConfig[$name] : $default;
    }
}
