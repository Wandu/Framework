<?php
namespace Wandu\View;

use Exception;
use Throwable;
use Wandu\View\Contracts\RenderInterface;

class PhpView implements RenderInterface
{
    /** @var string */
    protected $basePath;

    /** @var array */
    protected $values = [];

    /**
     * @param string $basePath
     */
    public function __construct($basePath = '')
    {
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
        $values = $values + $this->values;
        
        $_startObLevel = ob_get_level();
        ob_start();
        extract($values);
        try {
            require "{$basePath}/{$template}";
        } catch (Exception $e) {
            $this->cleanOutputBuffer($_startObLevel);
            throw $e;
        } catch (Throwable $e) {
            $this->cleanOutputBuffer($_startObLevel);
            throw $e;
        }
        return $this->cleanOutputBuffer($_startObLevel);
    }

    /**
     * @param int $startObLevel
     * @return string
     */
    protected function cleanOutputBuffer($startObLevel)
    {
        $contents = '';
        while (ob_get_level() - $startObLevel > 0) {
            $contents = ob_get_contents() . $contents;
            ob_end_clean();
        }
        return $contents;
    }
}
