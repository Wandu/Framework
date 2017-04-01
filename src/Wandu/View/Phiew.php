<?php
namespace Wandu\View;

use Wandu\View\Contracts\RenderInterface;
use Wandu\View\Phiew\Contracts\ResolverInterface;

class Phiew implements RenderInterface
{
    /** @var \Wandu\View\Phiew\Contracts\ResolverInterface */
    protected $resolver;

    /** @var array */
    protected $attributes = [];
    
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $attributes = [])
    {
        $new = clone $this;
        $new->attributes = array_merge($new->attributes, $attributes);
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function render($template, array $attributes = [], $basePath = null)
    {
        $template = $this->resolver->resolve($template);
        return $template->execute($attributes + $this->attributes);
    }
}
