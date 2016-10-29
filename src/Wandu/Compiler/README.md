Wandu Compiler
===

[![Latest Stable Version](https://poser.pugx.org/wandu/compiler/v/stable.svg)](https://packagist.org/packages/wandu/compiler)
[![Latest Unstable Version](https://poser.pugx.org/wandu/compiler/v/unstable.svg)](https://packagist.org/packages/wandu/compiler)
[![Total Downloads](https://poser.pugx.org/wandu/compiler/downloads.svg)](https://packagist.org/packages/wandu/compiler)
[![License](https://poser.pugx.org/wandu/compiler/license.svg)](https://packagist.org/packages/wandu/compiler)

**[EXPERIMENT]** PHP Base Compiler(Lexical Analyzer).

## Installation

```bash
composer require wandu/compiler
```

## Useage

### LexicalAnalyzer

**Example.**

```php
$lexer = new \Wandu\Compiler\LexicalAnalyzer([
    '\\+' => function () {
        return 't_add';
    },
    '\\-' => function () {
        return 't_minus';
    },
    '\\*' => function () {
        return 't_multi';
    },
    '\\/' => function () {
        return 't_divide';
    },
    '\\=' => function () {
        return 't_equal';
    },
    '[1-9][0-9]*|0([0-7]+|(x|X)[0-9A-Fa-f]*)?' => function ($word) {
        return "t_number";
    },
    '\s' => null,
]);

$lexer->analyze('10 + 20 = 0')); // ['t_number', 't_add', 't_number', 't_equal', 't_number',]
```

## References

- Lexer : http://nikic.github.io/2011/10/23/Improving-lexing-performance-in-PHP.html
