<?php
namespace Wandu\Foundation\Bootstrapper;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Wandu\Foundation\Application;
use Wandu\Http\Sender\ResponseSender;
use Wandu\Router\Contracts\Router;
use Wandu\Router\Dispatcher;

class HttpRouterBootstrapperTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
        $this->addToAssertionCount(1);
    }
    
    public function testBoot()
    {
        $sender = Mockery::mock(ResponseSender::class);
        $sender->shouldReceive('sendToGlobal')->with(Mockery::on(function (ResponseInterface $response) {
            static::assertInstanceOf(ResponseInterface::class, $response);
            static::assertSame(200, $response->getStatusCode());
            static::assertEquals("hello world", $response->getBody()->__toString());
            
            return true;
        }))->once();
        
        $bootstrap = new HttpRouterBootstrapper();
        $app = new Application($bootstrap);
        $app->boot();
        
        // override
        $app->instance(ResponseSender::class, $sender); // mocking
        $app->descriptor(Dispatcher::class)->after(function (Dispatcher $dispatcher) {
            $dispatcher->setRoutes(function (Router $router) {
                $router->get('/', HttpRouterBootstrapperTestController::class);
            });
        });

        $app->execute();
    }
}

class HttpRouterBootstrapperTestController
{
    public function index()
    {
        return "hello world";
    }
}
