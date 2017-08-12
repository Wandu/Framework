<?php
namespace Wandu\Restifier\Sample;

/**
 * @property-read string $address
 * @property-read array $paymentmethods
 */
class SampleCustomer
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
