Wandu View
===

[![Latest Stable Version](https://poser.pugx.org/wandu/view/v/stable.svg)](https://packagist.org/packages/wandu/view)
[![Latest Unstable Version](https://poser.pugx.org/wandu/view/v/unstable.svg)](https://packagist.org/packages/wandu/view)
[![Total Downloads](https://poser.pugx.org/wandu/view/downloads.svg)](https://packagist.org/packages/wandu/view)
[![License](https://poser.pugx.org/wandu/view/license.svg)](https://packagist.org/packages/wandu/view)


### Tempy

Blade 템플릿과 Latte 템플릿의 좋은 점만 가지고 오고 싶었습니다. 문서가 우선, 그리고 개발을 해보려고 합니다.

## Documentation

### 변수(Variable)

**일반적인 변수 사용**

```tempy
{{ $foo }}
```

```php
<?php echo $foo ?>
```

**기본값(Default Value)**

PHP7의 문법이 편리하기 때문에 그대로 가지고 왔습니다.

```tempy
{{ $foo ?? "default" }}
```

```php
<?php echo isset($foo) ? $foo : "default" ?>
```

### 조건문(Condition)

```tempy
{{ if ($foo === 1) }}
if text
{{ elseif ($foo === 2) }}
elseif text
{{ else }}
else text
{{ endif }}
```

```php
<?php if ($foo === 1) : ?>
if text
<?php elseif ($foo === 2) : ?>
elseif text
<?php else : ?>
else text
<?php endif; ?>
```

### 반복문(Loop/Iterator)

**기본 루프**

예시는 `for`이지만 `foreach`, `while` 모두 사용가능합니다.

```tempy
{{ for ($i = 0; $i < 3; $i++) }}
{{ endfor }}
```

```php
<?php for ($i = 0; $i < 3; $i++) : ?>
<?php endfor?>
```

**빈 상태(Empty State)**

```tempy
{{ for ($i = 0; $i < 3; $i++) }}
loop!
{{ empty }}
empty!
{{ endfor }}
```

```php
<?php $__iter = 0; for ($i = 0; $i < 3; $i++) : $__iter++; ?>
loop!
<?php endfor; if ($__iter === 0) : ?>
empty!
<?php endif; unset($__iter) ?>
```

**Todo**

```tempy
{{ first }}{{ endfirst }}
{{ last }}{{ endlast }}
```

### Layout

```tempy
{{ layout "layout/master" with [
    "title" => "Hello World",
    "sidebar" => "sidebar/01",
    "message" => $message,
    "footer" => "footer/01"
] }}
Main Message~
```

```tempy
<header>{{ $title }}</header>
<aside>{{ include $sidebar }}</aside>
{{ yield }}
{{ $message }}
<footer>{{ include $footer with [
    "title" => "footer - {$title}",
    "message" => "Hello World, this is Footer!",
] }}</footer>
```

### HTML Helpers

ref. `latte/latte`

**Inputs**

```tempy
<select tempy:value="$value">
    <option value="1">option 1</option>
    <option value="2">option 2</option>
    <option value="3">option 3</option>
    <option value="4">option 4</option>
</select>
```

```php

```
## 고급 사용

### Add Macro

### Pre-Process / Runtime-Process


## References

- **Lexer**: http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
