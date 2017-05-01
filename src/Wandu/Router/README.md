Wandu Router
===

[![Latest Stable Version](https://poser.pugx.org/wandu/router/v/stable.svg)](https://packagist.org/packages/wandu/router)
[![Latest Unstable Version](https://poser.pugx.org/wandu/router/v/unstable.svg)](https://packagist.org/packages/wandu/router)
[![Total Downloads](https://poser.pugx.org/wandu/router/downloads.svg)](https://packagist.org/packages/wandu/router)
[![License](https://poser.pugx.org/wandu/router/license.svg)](https://packagist.org/packages/wandu/router)

FastRoute with PSR-7 Wrapper Library.

## Installation

```bash
composer require wandu/router
```

##

/users/:id
/users
/users/:id/comments
=> 

~^
    /users/(?:
        /([^/]+)
      | ()
      | /([^/]+)/comments
    )
$~x

## Websites

- [wandu.github.io/#router](https://wandu.github.io/#router)

## Reference

 - [nikic/FastRoute](https://github.com/nikic/FastRoute).
