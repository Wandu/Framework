<?php
namespace Wandu\Config;

use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Config\Exception\NotAllowedMethodException;
use Wandu\Support\DotArray;

class Config extends DotArray implements ConfigInterface
{
    /** @var bool */
    protected $readOnly;

    /**
     * @param array $items
     * @param bool $readOnly
     */
    public function __construct(array $items = [], $readOnly = true)
    {
        parent::__construct($items);
        $this->readOnly = $readOnly;
    }
    
    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        if ($this->readOnly) {
            throw new NotAllowedMethodException();
        }
        return parent::set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        if ($this->readOnly) {
            throw new NotAllowedMethodException();
        }
        return parent::remove($name);
    }
}
