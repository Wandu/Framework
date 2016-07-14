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

class DefaultHttpErrorHandler implements HttpErrorHandlerInterface
{
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
        $statusCode = 500;
        $reasonPhrase = 'Internal Server Error';
        $attributes = [];
        if ($exception instanceof HttpException) {
            if ($exception->getBody()) {
                return $exception;
            }
            $statusCode = $exception->getStatusCode();
            $reasonPhrase = $exception->getReasonPhrase();
            $attributes = $exception->getAttributes();

            $this->logger->info($this->prettifyRequest($request));
            $this->logger->info($exception);
        } elseif ($this->config->get('debug', true)) {
            $whoops = $this->getWhoops($request);
            return \Wandu\Http\create($whoops->handleException($exception), $statusCode);
        } else {
            $this->logger->error($this->prettifyRequest($request));
            $this->logger->error($exception);
        }
        if ($this->isAjax($request)) {
            return \Wandu\Http\json(array_merge([
                'status' => $statusCode,
                'reason' => $reasonPhrase,
            ], $attributes), $statusCode);
        }

        // 에러화면에서는 어떤에러인지 메시지를 출력해서는 안된다.
        return \Wandu\Http\create("{$statusCode} {$reasonPhrase}", $statusCode);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return bool
     */
    protected function isAjax(ServerREquestInterface $request)
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
        $contents .= "BODY\n";
        $contents .= "\"{$request->getBody()->__toString()}\"\n";
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
