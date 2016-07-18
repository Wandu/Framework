<?php
namespace Wandu\Bridges\Latte;

use Latte\Engine;
use Latte\Loaders\FileLoader;
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
     * @param string $basePath
     * @param string $tempPath
     */
    public function __construct($basePath, $tempPath = null)
    {
        $this->latte = new Engine();
        $this->latte->setLoader(new FileLoader());
        if ($tempPath) {
            $this->latte->setTempDirectory($tempPath);
        }
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
        if (!file_exists("{$basePath}/{$template}.latte")) {
            throw new FileNotFoundException("Cannot find the file, {$basePath}/{$template}.latte");
        }
        return $this->latte->renderToString("{$basePath}/{$template}.latte" , $values + $this->values);
    }
}
