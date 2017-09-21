<?php
namespace Wandu\Validator;

use InvalidArgumentException;

class TargetName
{
    const PATTERN = <<<'REGEXP'
([a-zA-Z_][a-zA-Z0-9_-]*)((?:\[\d*\])*)(\?)?
REGEXP;

    static public function parse(string $target): TargetName
    {
        if (!preg_match('~^' . static::PATTERN . '$~ux', $target, $matches)) {
            preg_match('~' . static::PATTERN . '~ux', $target, $matches);
            $name = $matches[0];
            throw new InvalidArgumentException(
                "Invalid target name. Did you mean this? '{$name}'."
            );
        }
        $iterator = [];
        if ($matches[2]) {
            $iterator = array_map(function ($iterator) {
                if ($iterator === '') return null;
                return (int) $iterator;
            }, explode('][', rtrim(ltrim($matches[2], '['), ']')));
        }
        return new static($matches[1], $iterator, !!($matches[3] ?? null));
    }
    
    /** @var string */
    protected $name;
    
    /** @var array */
    protected $iterator;

    /** @var bool */
    protected $optional;

    public function __construct(string $name, array $iterator = [], bool $optional)
    {
        $this->name = $name;
        $this->iterator = $iterator;
        $this->optional = $optional;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getIterator(): array
    {
        return $this->iterator;
    }

    /**
     * @return boolean
     */
    public function isOptional(): bool
    {
        return $this->optional;
    }
}
