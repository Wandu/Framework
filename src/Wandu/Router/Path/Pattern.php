<?php
namespace Wandu\Router\Path;

use Wandu\Router\Exception\CannotGetPathException;

class Pattern
{
    const VARIABLE_REGEX = <<<'REGEX'
(\\.)
|
([\/.])?
(?:
    (?:
        \:(\w+)
        (?:
            \(
            ( (?:\\. | [^\\()])+ )
            \)
        )?
     |  \(
        ( (?:\\. | [^\\()])+ )
        \)
        
    )
    ([?])?
    |(\*)
)
REGEX;
    // ([?])?
    // ([+*?])?
    
    const DEFAULT_DISPATCH_REGEX = '[^/]+';

    /** @var array */
    protected $parsedPattern;

    /** @var array */
    protected $required = [];

    /** @var array */
    protected $optional = [];
    
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return array
     */
    public function parse() {
        if ($this->parsedPattern) {
            return $this->parsedPattern;
        }
        if (!preg_match_all(
            '~' . self::VARIABLE_REGEX . '~x', $this->pattern, $matches,
            PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        )) {
            return $this->parsedPattern = [
                [$this->pattern]
            ];
        }
        
        $parsedPattern = [];
        $this->getOptional($matches);
        if ($countOptionals = count($this->optional)) {
            foreach ($this->subset($this->optional) as $optional) {
                $parsedPattern[] = $this->parseEach($this->pattern, $matches, $optional);
            }
        } else {
            $parsedPattern[] = $this->parseEach($this->pattern, $matches);
        }
        return $this->parsedPattern = $parsedPattern;
    }

    /**
     * @param array $arguments
     * @return string
     */
    public function path(array $arguments = [])
    {
        $parsedPattern = $this->parse();
        foreach ($this->required as $required) {
            if (!array_key_exists($required, $arguments)) {
                throw new CannotGetPathException($this->required);
            }
        }
        $patternIndex = 0;
        foreach ($this->optional as $index => $optional) {
            if (array_key_exists($optional, $arguments)) {
                $patternIndex += (1 >> $index);
            }
        }
        $path = '';
        foreach ($parsedPattern[$patternIndex] as $item) {
            if (is_array($item)) {
                if ($item[0]) {
                    $path .= rawurlencode($arguments[$item[0]]);
                    unset($arguments[$item[0]]);
                } elseif (strpos($item[1], '(?:\/') === 0) {
                    $path .= '/';
                }
            } else {
                $path .= $item;
            }
        }
        $queries = [];
        foreach ($arguments as $key => $value) {
            $value = rawurlencode($value);
            $queries[] = "{$key}={$value}";
        }
        return rtrim($path . (count($queries) ? '?' . implode('&', $queries) : ''), '/');
    }
    
    private function parseEach($route, $matches, array $optional = [])
    {
        $offset = 0;
        $routeData = [];
        foreach ($matches as $set) {
            if ($set[0][1] > $offset) {
                $routeData[] = substr($route, $offset, $set[0][1] - $offset);
            }
            $prefix = $set[2][0] ?? '';
            $name = $set[3][0] ?? '';
            $capture = $set[4][0] ?? '';
            $group = $set[5][0] ?? '';
            $modifier = $set[6][0] ?? '';
            $asterisk = $set[7][0] ?? '';

            // group
            if ($group) {
                if ($prefix) {
                    $routeData[] = ['', "(?:\\/{$group}){$modifier}",];
                } else {
                    $routeData[] = ['', "(?:{$group}){$modifier}",];
                }
            } elseif ($asterisk) {
                if ($prefix) {
                    $routeData[] = ['', '\/.*'];
                } else {
                    $routeData[] = ['', '.*'];
                }
            } elseif (in_array($name, $optional) || $modifier !== '?') {
                if ($prefix) {
                    $routeData[] = $prefix;
                }
                $routeData[] = [$name, $capture ?: static::DEFAULT_DISPATCH_REGEX];
            }
            $offset = $set[0][1] + strlen($set[0][0]);
        }
        if ($offset != strlen($route)) {
            $routeData[] = substr($route, $offset);
        }
        return $routeData;
    }
    
    private function getOptional($matches)
    {
        $optional = [];
        $required = [];
        foreach ($matches as $set) {
            $name = $set[3][0] ?? '';
            $modifier = $set[6][0] ?? '';
            $group = $set[5][0] ?? '';
            $asterisk = $set[7][0] ?? '';

            if ($group || $asterisk) { // 
            } elseif ($modifier === '?') {
                $optional[] = $name;
            } else {
                $required[] = $name;
            }
        }
        $this->optional = $optional;
        $this->required = $required;
    }
    
    private function subset($elems) {
        $count = count($elems);
        if ($count === 0) return;
        $subsetCount = pow(2, $count);
        for ($i = 0; $i < $subsetCount; $i++) {
            $out = [];
            for ($j = 0; $j < $count; $j++) {
                if ($i & (1 << $j)) $out[] = $elems[$j];
            }
            yield $out;
        }
    }
}
