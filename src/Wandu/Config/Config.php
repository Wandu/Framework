<?php
namespace Wandu\Config;

use InvalidArgumentException;
use Wandu\Config\Contracts\Config as ConfigContract;
use Wandu\Config\Contracts\Loader;
use Wandu\Config\Exception\CannotLoadException;
use Wandu\Config\Exception\NotAllowedMethodException;

class Config implements ConfigContract
{
    /** @var array */
    protected $items;
    
    /** @var \Wandu\Config\Contracts\Loader[] */
    protected $loaders = [];

    /**
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param \Wandu\Config\Contracts\Loader $loader
     */
    public function pushLoader(Loader $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * @param string $path
     */
    public function load(string $path)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->test($path)) {
                $this->merge($loader->load($path));
                return;
            }
        }
        throw new CannotLoadException($path);
    }

    /**
     * @param array $appender
     */
    public function merge(array $appender)
    {
        $this->items = $this->recursiveMerge($this->items, $appender);
    }

    /**
     * @param mixed $origin
     * @param mixed $appender
     * @return mixed
     */
    private function recursiveMerge($origin, $appender)
    {
        if (is_array($origin)
            && array_values($origin) !== $origin
            && is_array($appender)
            && array_values($appender) !== $appender) {
            foreach ($appender as $key => $value) {
                if (isset($origin[$key])) {
                    $origin[$key] = $this->recursiveMerge($origin[$key], $value);
                } else {
                    $origin[$key] = $value;
                }
            }
            return $origin;
        }
        return $appender;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->items ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function has($name): bool
    {
        if ($name === '') {
            return true;
        }
        $names = explode('.', $name);
        $dataToReturn = $this->items;
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
    public function get($name, $default = null)
    {
        if ($name === '') {
            return $this->items;
        }
        $names = explode('.', $name);
        $dataToReturn = $this->items;
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
    public function subset($name): ConfigContract
    {
        $subset = $this->get($name);
        if (!is_array($subset)) {
            throw new InvalidArgumentException('subset must be an array.');
        }
        return new static($subset);
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
        throw new NotAllowedMethodException(__FUNCTION__, __CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new NotAllowedMethodException(__FUNCTION__, __CLASS__);
    }
}
