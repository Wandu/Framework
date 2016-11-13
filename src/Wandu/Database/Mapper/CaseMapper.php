<?php
namespace Wandu\Database\Mapper;

use InvalidArgumentException;
use Wandu\Database\Contracts\MapperInterface;

class CaseMapper implements MapperInterface
{
    const SNAKECASE = 1; // snake_case
    const PASCALCASE = 2; // PascalCase
    const CAMELCASE = 3; // camelCase
    const KEBABCASE = 4; // kebab-case 
    
    /** @var int */
    private $from;

    /** @var int */
    private $to;

    /**
     * @param int $from
     * @param int $to
     */
    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * {@inheritdoc}
     */
    public function map($name)
    {
        // 4 cases, [SNAKECASE, SNAKECASE], [PASCALCASE, PASCALCASE], [CAMELCASE, CAMELCASE], [KEBABCASE, KEBABCASE]
        if ($this->from === $this->to) return $name;

        
        switch ($this->from) {
            case static::PASCALCASE:
                if ($this->to === static::CAMELCASE) return lcfirst($name);
                $tokens = $this->fromPascalCase($name);
                break;
            case static::CAMELCASE:
                if ($this->to === static::PASCALCASE) return ucfirst($name);
                $tokens = $this->fromPascalCase($name);
                break;
            case static::KEBABCASE:
                $tokens = $this->fromKebabCase($name);
                break;
            case static::SNAKECASE:
                $tokens = $this->fromSnakeCase($name);
                break;
            default:
                throw new InvalidArgumentException("Argument 1 passed to " . __METHOD__ . "() must be in [1, 2, 3, 4]");
        }
        switch ($this->to) {
            case static::PASCALCASE:
                return str_replace(' ', '', ucwords($tokens));
            case static::CAMELCASE:
                return lcfirst(str_replace(' ', '', ucwords($tokens)));
            case static::KEBABCASE:
                return str_replace(' ', '-', strtolower($tokens));
            case static::SNAKECASE:
                return str_replace(' ', '_', strtolower($tokens));
            default:
                throw new InvalidArgumentException("Argument 1 passed to " . __METHOD__ . "() must be in [1, 2, 3, 4]");
        }
    }

    private function fromSnakeCase($name)
    {
        return str_replace('_', ' ', $name);
    }

    private function fromKebabCase($name)
    {
        return str_replace('-', ' ', $name);
    }

    private function fromPascalCase($name)
    {
        return preg_replace('/((?<=[^$])[A-Z0-9])/', ' $1', $name);
    }
}
