<?php
namespace Wandu\Router\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use SessionHandlerInterface;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Exception\HttpException;
use Wandu\Http\Parameters\CookieJar;
use Wandu\Http\Parameters\Session;
use Wandu\Http\Session\Configuration;
use Wandu\Router\Contracts\MiddlewareInterface;

class Sessionify implements MiddlewareInterface
{
    /** @var \SessionHandlerInterface */
    protected $handler;
    
    /** @var \Wandu\Http\Session\Configuration */
    protected $config;

    /**
     * @param \SessionHandlerInterface $handler
     * @param \Wandu\Http\Session\Configuration $config
     */
    public function __construct(SessionHandlerInterface $handler, Configuration $config = null)
    {
        $this->handler = $handler;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        $cookieJar = new CookieJar($request);
        $session = new Session($cookieJar, $this->handler, $this->config);

        $request = $request
            ->withAttribute('cookie', $cookieJar)
            ->withAttribute(CookieJar::class, $cookieJar)
            ->withAttribute(CookieJarInterface::class, $cookieJar)
            ->withAttribute('session', $session)
            ->withAttribute(Session::class, $session)
            ->withAttribute(SessionInterface::class, $session);

        // run next
        try {
            $response = $next($request);
        } catch (HttpException $exception) {
            $response = $exception->getResponse();
            
            $session->applyToCookieJar($cookieJar);
            $exception->setResponse($cookieJar->applyToResponse($response));
            throw $exception;
        }

        $session->applyToCookieJar($cookieJar);
        return $cookieJar->applyToResponse($response);
    }
}
