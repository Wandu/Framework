<?php
namespace Wandu\Restifier\Resources;

use Wandu\Restifier\Contracts\TransformResource;

class ResourceAdapter implements TransformResource
{
    /** @var mixed */
    protected $item;
    
    /** @var callable */
    protected $rule;
    
    /** @var callable */
    protected $includeRule;
    
    public function __construct($item, callable $rule, callable $includeRule = null)
    {
        $this->item = $item;
        $this->rule = $rule;
        $this->includeRule = $includeRule;
    }

    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return call_user_func($this->rule, $this->item);
    }

    /**
     * {@inheritdoc}
     */
    public function includeAttribute(string $name)
    {
        if ($this->includeRule) {
            return call_user_func($this->includeRule, $this->item, $name) ?: [];
        }
        return [];
    }
}
