<?php
namespace Wandu\Config;

class DotConfig
{
    /** @var array */
    protected $config;

    /** @var bool */
    protected $readOnly;

    /**
     * @param array $config
     * @param bool $readOnly
     */
    public function __construct(array $config, $readOnly = true)
    {
        $this->config = $config;
        $this->readOnly = $readOnly;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->config;
    }

    public function get($name, $default = null)
    {
        if ($name === '') {
            return $this->config;
        }
        $names = explode('.', $name);
        $dataToReturn = $this->config;
        while (count($names)) {
            $name = array_shift($names);
            if (!is_array($dataToReturn) || !array_key_exists($name, $dataToReturn)) {
                return $default;
            }
            $dataToReturn = $dataToReturn[$name];
        }
        return $dataToReturn;
    }

    public function set($name, $value)
    {
        if ($this->readOnly) {
            throw new NotAllowedMethodException();
        }
        if ($name === '') {
            $this->config = $value;
        }
        $names = explode('.', $name);
        $dataToSet = &$this->config;
        while (count($names)) {
            $name = array_shift($names);
            if (!is_array($dataToSet)) {
                $dataToSet = [];
            }
            if (!array_key_exists($name, $dataToSet)) {
                $dataToSet[$name] = null;
            }
            $dataToSet = &$dataToSet[$name];
        }
        $dataToSet = $value;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function __invoke($name, $default = null)
    {
        return $this->get($name, $default);
    }
}
