<?php
namespace Wandu\View\Phiew;

class Buffer
{
    /** @var string */
    protected $buffer = '';

    /** @var array */
    protected $attributes = [];

    /** @var array */
    protected $contents = [];

    /** @var \Wandu\View\Phiew\Template */
    protected $layout;
    
    /** @var array */
    protected $layoutAttributes;

    /** @var string */
    protected $pushName;
    
    /** @var string */
    protected $sectionName;

    public function __construct(array $attributes = [], array $contents = [])
    {
        $this->attributes = $attributes;
        $this->contents = $contents;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->buffer;
    }

    /**
     * @param string $contents
     */
    public function write(string $contents)
    {
        $this->buffer .= $contents;
    }

    /**
     * @param \Wandu\View\Phiew\Template $template
     * @param array $attributes
     * @return string
     */
    public function resolveRequire(Template $template, array $attributes = [])
    {
        return $template->execute($attributes + $this->attributes);
    }
    
    /**
     * @param \Wandu\View\Phiew\Template $layout
     * @param array $attributes
     */
    public function setLayout(Template $layout, array $attributes = [])
    {
        $this->layout = $layout;
        $this->layoutAttributes = $attributes + $this->attributes;
    }
    
    public function resolveLayout()
    {
        if ($this->layout) {
            $this->buffer .= $this->layout->execute($this->layoutAttributes, $this->contents);
            $this->layout = null;
        }
    }

    /**
     * @param string $name
     */
    public function startPush(string $name)
    {
        $this->pushName = $name;
    }

    /**
     * @param string $contents
     */
    public function resolvePush(string $contents)
    {
        if (!$this->pushName) {
            throw new PhiewException('push is not started.', PhiewException::CODE_WRONG_SYNTAX);
        }

        $name = $this->pushName;
        $this->pushName = null;

        if (!isset($this->contents[$name])) {
            $this->contents[$name] = '';
        }
        $this->contents[$name] .= $contents;
    }

    /**
     * @param string $name
     */
    public function startSection(string $name)
    {
        $this->sectionName = $name;
    }

    /**
     * @param string $contents
     */
    public function resolveSection(string $contents)
    {
        if (!$this->sectionName) {
            throw new PhiewException('section is not started.', PhiewException::CODE_WRONG_SYNTAX);
        }
        $name = $this->sectionName;
        $this->sectionName = null;

        $this->contents[$name] = $contents;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getContent(string $name): string
    {
        return $this->contents[$name] ?? '';
    }
}
