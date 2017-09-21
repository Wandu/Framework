<?php
namespace Wandu\Sanitizer\Sample;

class SamplePoint
{
    /** @var array */
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
