<?php
namespace Wandu\Foundation\Error;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Foundation\Contracts\HttpErrorHandler;
use Wandu\Http\Exception\HttpException;
use Wandu\Router\Exception\MethodNotAllowedException as RouteMethodException;
use Wandu\Router\Exception\RouteNotFoundException;
use Wandu\Validator\Exception\InvalidValueException;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use function Wandu\Http\Response\create;
use function Wandu\Http\Response\json;

class DefaultHttpErrorHandler implements HttpErrorHandler
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    
    /** @var \Wandu\Config\Contracts\ConfigInterface */
    protected $config;

    /** @var array */
    protected $types = [
        /* ... */
    ];
    
    /** @var array */
    protected $statusCodes = [
        InvalidValueException::class => 400,
    ];

    /**
     * @param \Wandu\Config\Contracts\ConfigInterface $config
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(ConfigInterface $config, LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request, $exception)
    {
        if ($exception instanceof HttpException) {
            if ($this->logger) {
                $this->logger->notice($this->prettifyRequest($request));
                $this->logger->notice($exception);
            }
            return $exception;
        }
        if ($this->config->get('debug', true)) {
            $whoops = $this->getWhoops($request);
            return create($whoops->handleException($exception), 500);
        }

        $statusCode = 500;
        $reasonPhrase = 'Internal Server Error';

        if ($exception instanceof RouteNotFoundException) {
            $statusCode = 404;
            $reasonPhrase = "Not Found";
        } elseif ($exception instanceof RouteMethodException) {
            $statusCode = 405;
            $reasonPhrase = 'Method Not Allowed';
        }
        if ($this->logger) {
            $this->logger->error($this->prettifyRequest($request));
            $this->logger->error($exception);
        }

        if ($this->isAjax($request)) {
            return json([
                'status' => $statusCode,
                'reason' => $reasonPhrase,
            ], $statusCode);
        }
        return create("{$statusCode} {$reasonPhrase}", $statusCode);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return bool
     */
    protected function isAjax(ServerRequestInterface $request)
    {
        return $request->hasHeader('x-requested-with') &&
            $request->getHeaderLine('x-requested-with') === 'XMLHttpRequest';
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return string
     */
    protected function prettifyRequest(ServerRequestInterface $request)
    {
        $contents = "{$request->getMethod()} : {$request->getUri()->__toString()}\n";
        $contents .= "HEADERS\n";
        foreach ($request->getHeaders() as $name => $value) {
            $contents .= "    {$name} : {$request->getHeaderLine($name)}\n";
        }
        if ($body = $request->getBody()) {
            $contents .= "BODY\n";
            $contents .= "\"{$body->__toString()}\"\n";
        }
        return $contents;
    }
    
    protected function getWhoops(ServerRequestInterface $request)
    {
        $whoops = new Run();

        switch ($this->getAcceptType($request)) {
            case 'html':
                $whoops->pushHandler(new PrettyPageHandler());
                break;
            case 'json':
                $whoops->pushHandler(new JsonResponseHandler());
                break;
            default:
                $whoops->pushHandler(new PlainTextHandler());
        }

        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->sendHttpCode(false);

        return $whoops;
    }

    /**
     * @ref github.com/oscarotero/psr7-middlewares/blob/master/src/Middleware/FormatNegotiator.php
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return string
     */
    protected function getAcceptType(ServerRequestInterface $request)
    {
        $accept = $request->getHeaderLine('Accept');
        if (
            strpos($accept, 'text/html') !== false ||
            strpos($accept, 'application/xhtml+xml') !== false
        ) {
            return 'html';
        }
        if (
            strpos($accept, 'application/json') !== false ||
            strpos($accept, 'text/json') !== false ||
            strpos($accept, 'application/x-json') !== false
        ) {
            return 'json';
        }
        return 'text';
    }
}
