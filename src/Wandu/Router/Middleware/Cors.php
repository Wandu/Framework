<?php
namespace Wandu\Router\Middleware;

use Closure;
use Neomerx\Cors\Contracts\AnalysisResultInterface;
use Neomerx\Cors\Contracts\AnalyzerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\MiddlewareInterface;
use function Wandu\Http\Response\json;

class Cors implements MiddlewareInterface
{
    /** @var \Neomerx\Cors\Contracts\AnalyzerInterface */
    protected $analyzer;

    public function __construct(AnalyzerInterface $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        $cors = $this->analyzer->analyze($request);
        
        switch ($cors->getRequestType()) {
            case AnalysisResultInterface::ERR_NO_HOST_HEADER:
            case AnalysisResultInterface::ERR_ORIGIN_NOT_ALLOWED:
            case AnalysisResultInterface::ERR_METHOD_NOT_SUPPORTED:
            case AnalysisResultInterface::ERR_HEADERS_NOT_SUPPORTED:
                return $this->responseError($cors);
            case AnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST:
                return $this->responsePreFlight($cors);
            case AnalysisResultInterface::TYPE_REQUEST_OUT_OF_CORS_SCOPE:
                return $next($request);
        }
        return $this->applyHeaders($next($request), $cors->getResponseHeaders());
    }

    /**
     * @param \Neomerx\Cors\Contracts\AnalysisResultInterface $cors
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function responseError(AnalysisResultInterface $cors): ResponseInterface
    {
        return json([
            'success' => false,
            'code' => $cors->getRequestType(),
        ], 403);
    }
    
    /**
     * @param \Neomerx\Cors\Contracts\AnalysisResultInterface $cors
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function responsePreFlight(AnalysisResultInterface $cors): ResponseInterface
    {
        return $this->applyHeaders(
            json([
                'success' => true,
            ]),
            $cors->getResponseHeaders()
        );
    }
    
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function applyHeaders(ResponseInterface $response, array $headers = []): ResponseInterface
    {
        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        return $response;
    }
}
