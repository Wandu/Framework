<?php
namespace PHPSTORM_META {
    $STATIC_METHOD_TYPES = [
        \Psr\Http\Message\ServerRequestInterface::getAttribute('') => [
            "session" instanceof \Wandu\Http\Contracts\SessionInterface,
            "cookie" instanceof \Wandu\Http\Contracts\CookieJarInterface,
            'server_params' instanceof \Wandu\Http\Contracts\ServerParamsInterface,
            'query_params' instanceof \Wandu\Http\Contracts\QueryParamsInterface,
            'parsed_body' instanceof \Wandu\Http\Contracts\ParsedBodyInterface,
        ],
    ];
}
