<?php
namespace Wandu\Compiler;

class Token
{
    protected $name;

    protected $value;

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __construct($name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
