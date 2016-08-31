<?php
namespace Wandu\Router\ClassLoader;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\DI\Container;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Exception\CannotFindParameterException;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\ParsedBodyInterface;
use Wandu\Http\Contracts\QueryParamsInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Cookie\CookieJar;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Parameters\QueryParams;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Http\Session\Session;
use Wandu\Router\Exception\HandlerNotFoundException;

class WanduLoaderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreate()
    {
        $loader = new WanduLoader(new Container());

        static::assertInstanceOf(TestStubInLoader::class, $loader->create(TestStubInLoader::class));
    }

    public function testCreateFail()
    {
        $loader = new WanduLoader(new Container());

        try {
            $loader->create('ThereIsNoClass');
            static::fail();
        } catch (HandlerNotFoundException $exception) {
            static::assertEquals('ThereIsNoClass', $exception->getClassName());
            static::assertNull($exception->getMethodName());
        }
    }

    public function testCall()
    {
        $loader = new WanduLoader(new Container());

        $request = new ServerRequest();
        $instance = new TestStubInLoader();

        static::assertEquals(
            'callFromLoader@StubInLoader',
            $loader->call($request, $instance, 'callFromLoader')
        );
    }

    public function testCallFromMagicMethod()
    {
        $loader = new WanduLoader(new Container());

        $request = new ServerRequest();
        $instance = new TestStubInLoader();

        static::assertEquals(
            '__call->callFromMagicMethod@StubInLoader',
            $loader->call($request, $instance, 'callFromMagicMethod')
        );
    }
    
    public function testQueryParamsAndParsedBody()
    {
        $loader = new WanduLoader(new Container());

        $request = new ServerRequest();
        $instance = new TestStubInLoader();

        static::assertTrue($loader->call($request, $instance, 'equalQueryParams'));
        static::assertTrue($loader->call($request, $instance, 'equalParsedBody'));
    }

    public function testCookieAndSession()
    {
        $loader = new WanduLoader(new Container());

        $request = new ServerRequest();
        $instance = new TestStubInLoader();

        // error
        try {
            $loader->call($request, $instance, 'equalCookie');
            static::fail();
        } catch (CannotResolveException $e) {
        }
        try {
            $loader->call($request, $instance, 'equalSession');
            static::fail();
        } catch (CannotResolveException $e) {
        }

        $request = $request->withAttribute('cookie', new CookieJar([]));
        $request = $request->withAttribute('session', new Session('id', []));

        static::assertTrue($loader->call($request, $instance, 'equalCookie'));
        static::assertTrue($loader->call($request, $instance, 'equalSession'));
    }
}

class TestStubInLoader
{
    public function __call($name, $arguments = [])
    {
        return "__call->{$name}@StubInLoader";
    }

    public function callFromLoader()
    {
        return "callFromLoader@StubInLoader";
    }

    public function equalQueryParams(
        QueryParams $queryParams,
        QueryParamsInterface $queryParamsInterface,
        ServerRequestInterface $request,
        ContainerInterface $container
    ) {
        return $queryParams === $queryParamsInterface
            && $queryParams === $request->getAttribute('query_params')
            && $container->get('request') === $request
            && $container->get(ServerRequest::class) === $request
            && $container->get(ServerRequestInterface::class) === $request;
    }

    public function equalParsedBody(
        ParsedBody $parsedBody,
        ParsedBodyInterface $parsedBodyInterface,
        ServerRequestInterface $request,
        ContainerInterface $container
    ) {
        return $parsedBody === $parsedBodyInterface
            && $parsedBody === $request->getAttribute('parsed_body')
            && $container->get('request') === $request
            && $container->get(ServerRequest::class) === $request
            && $container->get(ServerRequestInterface::class) === $request;
    }
    
    public function equalCookie(
        CookieJar $cookie,
        CookieJarInterface $cookieInterface,
        ServerRequestInterface $request,
        ContainerInterface $container
    ) {
        return $cookie === $cookieInterface
            && $cookie === $request->getAttribute('cookie')
            && $container->get('request') === $request
            && $container->get(ServerRequest::class) === $request
            && $container->get(ServerRequestInterface::class) === $request;
    }
    
    public function equalSession(
        Session $session,
        SessionInterface $sessionInterface,
        ServerRequestInterface $request,
        ContainerInterface $container
    ) {
        return $session === $sessionInterface
            && $session === $request->getAttribute('session')
            && $container->get('request') === $request
            && $container->get(ServerRequest::class) === $request
            && $container->get(ServerRequestInterface::class) === $request;
    }
}
