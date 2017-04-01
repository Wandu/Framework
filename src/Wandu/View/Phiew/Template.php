<?php
namespace Wandu\View\Phiew;

use Exception;
use SplFileObject;
use Throwable;
use Closure;
use Wandu\View\Phiew\Contracts\ResolverInterface;

class Template
{
    /** @var \SplFileObject */
    protected $file;
    
    /** @var \Wandu\View\Phiew\Contracts\ResolverInterface */
    protected $resolver;

    /** @var int */
    protected $level;
    
    /** @var \Wandu\View\Phiew\Buffer */
    protected $buffer;
    
    public function __construct(SplFileObject $file, ResolverInterface $resolver)
    {
        $this->file = $file;
        $this->resolver = $resolver;
    }

    /**
     * @internal
     * @param array $attributes
     * @param array $contents
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function execute(array $attributes = [], array $contents = [])
    {
        if ($this->buffer !== null) {
            throw new PhiewException('this template already rendering!', PhiewException::CODE_ALREADY_RENDERING);
        }
        $this->buffer = new Buffer($attributes, $contents);

        $this->level = ob_get_level();
        ob_start();
        extract($attributes);

        try {
            require $this->file->getRealPath();
            $this->buffer->resolveLayout();
        } catch (Exception $e) {
            while (ob_get_level() > $this->level) ob_end_clean();
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $this->level) ob_end_clean();
            throw $e;
        }
        
        $this->buffer->write($this->clean());
        $buffer = $this->buffer->__toString();
        $this->buffer = null;
        return $buffer;
    }

    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function render(string $name, array $attributes = [])
    {
        return $this->buffer->resolveRequire($this->resolver->resolve($name), $attributes);
    }

    /**
     * @param string $name
     * @param array $attributes
     */
    public function layout(string $name, array $attributes = [])
    {
        $this->syntax(function ($buffer) use ($name, $attributes) {
            $this->buffer->write($buffer);
            $this->buffer->setLayout($this->resolver->resolve($name), $attributes);
        });
    }
    
    public function endlayout()
    {
        $this->syntax(function () {
            $this->buffer->resolveLayout();
        });
    }

    /**
     * @param string $name
     */
    protected function push(string $name)
    {
        $this->syntax(function () use ($name) {
            $this->buffer->startPush($name);
        });
    }
    
    public function endpush()
    {
        $this->syntax(function ($buffer) {
            $this->buffer->resolvePush($buffer);
        });
    }
    
    public function section(string $name)
    {
        $this->syntax(function () use ($name) {
            $this->buffer->startSection($name);
        });
    }
    
    public function endsection()
    {
        $this->syntax(function ($buffer) {
            $this->buffer->resolveSection($buffer);
        });
    }
    
    public function content(string $name): string
    {
        return $this->buffer->getContent($name);
    }

    /**
     * @internal
     * @param \Closure $handle
     */
    protected function syntax(Closure $handle)
    {
        $handle($this->clean());
        ob_start();
    }
    
    /**
     * @internal
     * @return string
     */
    protected function clean()
    {
        $contents = '';
        while (ob_get_level() - $this->level > 0) {
            $contents = ob_get_contents() . $contents;
            ob_end_clean();
        }
        return $contents;
    }
}
