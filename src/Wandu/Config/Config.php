<?php
namespace Wandu\Config;

use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Config\Exception\NotAllowedMethodException;

class Config implements ConfigInterface
{
    /** @var array */
    protected $dataSet;

    /** @var bool */
    protected $readOnly;

    /**
     * @param array $dataSet
     * @param bool $readOnly
     */
    public function __construct(array $dataSet, $readOnly = true)
    {
        $this->dataSet = $dataSet;
        $this->readOnly = $readOnly;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawData()
    {
        return $this->dataSet;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        if ($name === '') {
            return $this->dataSet;
        }
        $names = explode('.', $name);
        $dataToReturn = $this->dataSet;
        while (count($names)) {
            $name = array_shift($names);
            if (!is_array($dataToReturn) || !array_key_exists($name, $dataToReturn)) {
                return $default;
            }
            $dataToReturn = $dataToReturn[$name];
        }
        return $dataToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        if ($this->readOnly) {
            throw new NotAllowedMethodException();
        }
        if ($name === '') {
            $this->dataSet = $value;
        }
        $names = explode('.', $name);
        $dataToSet = &$this->dataSet;
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
     * {@inheritdoc}
     */
    public function has($name)
    {
        if ($name === '') {
            return true;
        }
        $names = explode('.', $name);
        $dataToReturn = $this->dataSet;
        while (count($names)) {
            $name = array_shift($names);
            if (!is_array($dataToReturn) || !array_key_exists($name, $dataToReturn)) {
                return false;
            }
            $dataToReturn = $dataToReturn[$name];
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        if ($this->readOnly) {
            throw new NotAllowedMethodException();
        }
        if ($name === '') {
            $this->dataSet = [];
            return true;
        }
        $names = explode('.', $name);
        $dataToReturn = &$this->dataSet;
        while (count($names)) {
            $name = array_shift($names);
            if (!is_array($dataToReturn) || !array_key_exists($name, $dataToReturn)) {
                return false;
            }
            if (count($names) === 0) {
                unset($dataToReturn[$name]);
            } else {
                $dataToReturn = &$dataToReturn[$name];
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (strpos($offset, '||') !== false) {
            list($offset, $default) = explode('||', $offset);
            return $this->get($offset, $default);
        }
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
