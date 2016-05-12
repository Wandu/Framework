Wandu Compiler
===

[![Latest Stable Version](https://poser.pugx.org/wandu/compiler/v/stable.svg)](https://packagist.org/packages/wandu/compiler)
[![Latest Unstable Version](https://poser.pugx.org/wandu/compiler/v/unstable.svg)](https://packagist.org/packages/wandu/compiler)
[![Total Downloads](https://poser.pugx.org/wandu/compiler/downloads.svg)](https://packagist.org/packages/wandu/compiler)
[![License](https://poser.pugx.org/wandu/compiler/license.svg)](https://packagist.org/packages/wandu/compiler)

PHP Base Compiler(Lexical Analyzer).

템플릿 엔진을 만들다가, 이런거 하나 만들어두면 언젠가 자체 스크립트 만들때 두고두고 써먹을 수 있겠다라는 생각이 들었습니다.

## Lexical Analyer

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

## Syntax Analyzer (Todo)

**Example.**

```php
$syntaxer = new SyntaxAnalyzer(new LexicalAnalyzer([
    '\\+' => function () {
        return 'T_ADD';
    },
    '\\-' => function () {
        return 'T_MINUS';
    },
    '\\*' => function () {
        return 'T_MULTI';
    },
    '\\/' => function () {
        return 'T_DIV';
    },
    '\\=' => function () {
        return 'T_EQUAL';
    },
    '[1-9][0-9]*|0([0-7]+|(x|X)[0-9A-Fa-f]*)?' => function ($word) {
        return new Token("T_NUMBER", $word);
    },
    '\s' => null,
]));

$syntaxer->addToken(['T_ADD', 'T_MINUS', 'T_MULTI', 'T_DIV', 'T_EQUAL']);

$syntaxer->setRootSyntax('root');
$syntaxer->addSyntax('root', ['formula'], function ($x) {
    return $x;
});
$syntaxer->addSyntax('formula', ['T_NUMBER', 'T_MINUS', 'T_NUMBER'], function ($x, $_, $y) {
    return $x - $y;
});
$syntaxer->addSyntax('formula', ['T_NUMBER', 'T_MINUS', 'T_NUMBER'], function ($x, $_, $y) {
    return $x - $y;
});
$syntaxer->addSyntax('formula', ['T_NUMBER', 'T_ADD', 'T_NUMBER'], function ($x, $_, $y) {
    return $x + $y;
});
$syntaxer->addSyntax('formula', ['T_NUMBER', 'T_MULTI', 'T_NUMBER'], function ($x, $_, $y) {
    return $x * $y;
});
$syntaxer->addSyntax('formula', ['T_NUMBER', 'T_DIV', 'T_NUMBER'], function ($x, $_, $y) {
    return $x / $y;
});

$syntaxer->analyze('10 + 20'); // 30
```

## References

- Lexer : http://nikic.github.io/2011/10/23/Improving-lexing-performance-in-PHP.html
