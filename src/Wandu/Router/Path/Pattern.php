<?php
namespace Wandu\Router\Path;

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

    public function __construct(string $pattern, array $options = [])
    {
        $this->pattern = $pattern;
        $this->options = $options + ['delimiter' => '/']; // default
    }

    public function parse() {
        if (!preg_match_all(
            '~' . self::VARIABLE_REGEX . '~x', $this->pattern, $matches,
            PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        )) {
            return [
                [$this->pattern]
            ];
        }
        
        $routeData = [];
        $optionals = $this->getOptional($matches);
        if ($countOptionals = count($optionals)) {
            foreach ($this->subset($optionals) as $optional) {
                $routeData[] = $this->getRouteData($this->pattern, $matches, $optional);
            }
        } else {
            $routeData[] = $this->getRouteData($this->pattern, $matches);
        }
        return $routeData;
    }
    
    private function getRouteData($route, $matches, array $optional = [])
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
                    $routeData[] = $prefix;
                }
                $routeData[] = [
                    '',
                    '(?:' . $group . ')' . $modifier,
                ];
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
        foreach ($matches as $set) {
            $name = $set[3][0] ?? '';
            $modifier = $set[6][0] ?? '';
            $group = $set[5][0] ?? '';
            if (!$group && $modifier === '?') {
                $optional[] = $name;
            }
        }
        return $optional;
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
