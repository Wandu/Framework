<?php
namespace Wandu\Restifier\Sample;

/**
 * @property-read $username
 * @property-read \Wandu\Restifier\Sample\SampleCustomer $customer
 */
class SampleUser
{
    /** @var array */
    protected $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->attributes[$name];
    }
}
