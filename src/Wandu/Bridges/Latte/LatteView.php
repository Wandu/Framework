<?php
namespace Festiv\View;

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
    public function render($template, array $values = [])
    {
        if (!file_exists("{$this->basePath}/{$template}.latte")) {
            throw new FileNotFoundException();
        }
        return $this->latte->renderToString("{$this->basePath}/{$template}.latte" , $values + $this->values);
    }
}
