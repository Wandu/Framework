<?php
namespace Wandu\View\Bridges\Twig;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Wandu\View\Contracts\RenderInterface;
use Wandu\View\FileNotFoundException;

class TwigView implements RenderInterface
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var array */
    protected $values = [];

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
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
        $twig = $this->twig;
        if (isset($basePath)) {
            $twig = clone $this->twig;
            $twig->setLoader(new Twig_Loader_Filesystem($basePath));
        }
        try {
            return $twig->render($template, $values + $this->values);
        } catch (\Exception $e) {
            throw new FileNotFoundException("Cannot find the template file, {$template}");
        }
    }
}
