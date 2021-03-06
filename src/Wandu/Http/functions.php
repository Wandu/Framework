<?php
namespace Wandu\Http
{

    use Wandu\Http\Factory\ResponseFactory;

    /**
     * @return \Wandu\Http\Factory\ResponseFactory
     */
    function response()
    {
        if (!isset(ResponseFactory::$instance)) {
            ResponseFactory::$instance = new ResponseFactory();
        }
        return ResponseFactory::$instance;
    }

    /**
     * @reference https://gist.github.com/Mulkave/65daabb82752f9b9a0dd
     * @param string $url
     * @return array|boolean
     */
    function parseUrl($url)
    {
        $parts = parse_url(preg_replace_callback('/[^:\/@?&=#]+/u', function ($matches) {
            return urlencode($matches[0]);
        }, $url));
        if ($parts === false) {
            return false;
        }
        foreach($parts as $name => $value) {
            $parts[$name] = ($name === 'port') ? $value : urldecode($value);
        }
        return $parts;
    }
}

namespace Wandu\Http\Response
{

    use Closure;
    use Generator;
    use Psr\Http\Message\ServerRequestInterface;
    use Traversable;
    use Wandu\Http\Exception\BadRequestException;
    use function Wandu\Http\response;

    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function create($content = null, $status = 200, array $headers = [])
    {
        return response()->create($content, $status, $headers);
    }

    /**
     * @param \Closure $area
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function capture(Closure $area, $status = 200, array $headers = [])
    {
        return response()->capture($area, $status, $headers);
    }

    /**
     * @param  string|array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function json($data = [], $status = 200, array $headers = [])
    {
        return response()->json($data, $status, $headers);
    }

    /**
     * @param  string  $file
     * @param  string  $name
     * @param  array  $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function download($file, $name = null, array $headers = [])
    {
        return response()->download($file, $name, $headers);
    }

    /**
     * @param string $path
     * @param array $queries
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function redirect($path, $queries = [], $status = 302, $headers = [])
    {
        $parsedQueries = [];
        foreach ($queries as $key => $value) {
            $parsedQueries[] = "{$key}=" . urlencode($value);
        }
        if (count($parsedQueries)) {
            $path .= '?' . implode('&', $parsedQueries);
        }
        return response()->redirect($path, $status, $headers);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Wandu\Http\Exception\BadRequestException
     */
    function back(ServerRequestInterface $request)
    {
        if ($request->hasHeader('referer')) {
            return redirect($request->getHeader('referer'));
        }
        throw new BadRequestException();
    }

    /**
     * @deprecated use iterator
     * 
     * @param \Generator $generator
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function generator(Generator $generator, $status = 200, array $headers = [])
    {
        return response()->iterator($generator, $status, $headers);
    }

    /**
     * @param \Traversable $iterator
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    function iterator(Traversable $iterator, $status = 200, array $headers = [])
    {
        return response()->iterator($iterator, $status, $headers);
    }
}
