<?php
namespace Wandu\Foundation\Error;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Foundation\Contracts\HttpErrorHandlerInterface;
use Wandu\Http\Exception\HttpException;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use function Wandu\Http\Response\create;
use function Wandu\Http\Response\json;

class DefaultHttpErrorHandler implements HttpErrorHandlerInterface
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    
    /** @var \Wandu\Config\Contracts\ConfigInterface */
    protected $config;
    
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Wandu\Config\Contracts\ConfigInterface $config
     */
    public function __construct(LoggerInterface $logger, ConfigInterface $config)
    {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request, $exception)
    {
        if ($exception instanceof HttpException) {
            $this->logger->notice($this->prettifyRequest($request));
            $this->logger->notice($exception);
            return $exception;
        }
        if ($this->config->get('debug', true)) {
            $whoops = $this->getWhoops($request);
            return create($whoops->handleException($exception), 500);
        }

        $statusCode = 500;
        $reasonPhrase = 'Internal Server Error';

        $this->logger->error($this->prettifyRequest($request));
        $this->logger->error($exception);

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
