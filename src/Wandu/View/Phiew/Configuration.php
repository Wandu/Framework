<?php
namespace Wandu\View\Phiew;

class Configuration
{
    /** @var \SplFileInfo[] */
    public $path = [];
    
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
