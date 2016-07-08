<?php
namespace Wandu\Router\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Cookie\CookieJarFactory;
use Wandu\Http\Exception\AbstractHttpException;
use Wandu\Http\Session\SessionFactory;
use Wandu\Router\Contracts\MiddlewareInterface;

class Sessionify implements MiddlewareInterface
{
    /** @var \Wandu\Http\Cookie\CookieJarFactory */
    protected $cookieJarFactory;

    /** @var \Wandu\Http\Session\SessionFactory */
    protected $sessionFactory;

    /**
     * @param \Wandu\Http\Cookie\CookieJarFactory $cookieJarFactory
     * @param \Wandu\Http\Session\SessionFactory $sessionFactory
     */
    public function __construct(
        CookieJarFactory $cookieJarFactory,
        SessionFactory $sessionFactory
    ) {
        $this->cookieJarFactory = $cookieJarFactory;
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        $cookieJar = $this->cookieJarFactory->fromServerRequest($request);
        $session = $this->sessionFactory->fromCookieJar($cookieJar);

        $request = $request
            ->withAttribute('cookie', $cookieJar)
            ->withAttribute('session', $session);

        // run next
        try {
            $response = $next($request);
        } catch (AbstractHttpException $exception) {
            $response = $exception->getResponse();
            $this->sessionFactory->toCookieJar($session, $cookieJar);
            $exception->setResponse($this->cookieJarFactory->toResponse($cookieJar, $response));
            throw $exception;
        }

        $this->sessionFactory->toCookieJar($session, $cookieJar);
        return $this->cookieJarFactory->toResponse($cookieJar, $response);
    }
}
