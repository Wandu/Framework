<?php
namespace PHPSTORM_META {
    $STATIC_METHOD_TYPES = [
        \Psr\Http\Message\ServerRequestInterface::getAttribute('') => [
            "session" instanceof \Wandu\Http\Contracts\SessionInterface,
            "cookie" instanceof \Wandu\Http\Contracts\CookieJarInterface,
        ],
    ];
}
