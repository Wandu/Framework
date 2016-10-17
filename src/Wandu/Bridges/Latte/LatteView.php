<?php
namespace Wandu\Bridges\Latte;

use Latte\Engine;
use Wandu\View\Contracts\RenderInterface;
use Wandu\View\FileNotFoundException;

class LatteView implements RenderInterface
{
    /** @var \Latte\Engine */
    protected $latte;

    /** @var string */
    protected $basePath;

    /** @var array */
    protected $values = [];

    /**
     * @param \Latte\Engine $latte
     * @param string $basePath
     */
    public function __construct(Engine $latte, $basePath = '')
    {
        $this->latte = $latte;
        $this->basePath = $basePath;
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $values = [])
    {
        $new = clone $this;
        $new->values = $values;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function render($template, array $values = [], $basePath = null)
    {
        if (!isset($basePath)) {
            $basePath = $this->basePath;
        }
        if (!file_exists("{$basePath}/{$template}")) {
            throw new FileNotFoundException("Cannot find the file, {$basePath}/{$template}");
        }
        return $this->latte->renderToString("{$basePath}/{$template}" , $values + $this->values);
    }
}
