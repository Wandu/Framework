<?php
namespace Wandu\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

class Response implements ResponseInterface
{
    use MessageTrait;

    protected $phrases = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /** @var int */
    protected $statusCode;

    /** @var string */
    protected $reasonPhrase;

    /**
     * @param int $statusCode
     * @param array $headers
     * @param StreamInterface $body
     */
    public function __construct(
        $statusCode = 200,
        $reasonPhrase = '',
        array $headers = [],
        StreamInterface $body = null
    ) {
        $this->validStatusCode($statusCode);

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $this->filterReasonPhrase($statusCode, $reasonPhrase);
        $this->initHeaders($headers);
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $this->validStatusCode($code);
        $new = clone $this;
        $new->statusCode = $code;
        $new->reasonPhrase = $this->filterReasonPhrase($code, $reasonPhrase);
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * @param int $code
     */
    protected function validStatusCode($code)
    {
        if (!is_numeric($code)) {
            throw new InvalidArgumentException("Invalid status code. It must be a 3-digit integer.");
        }
        if (!isset($this->phrases[$code])) {
            throw new InvalidArgumentException("Invalid status code \"{$code}\".");
        }
    }

    /**
     * @param $code
     * @param $reasonPhrase
     * @return mixed
     */
    protected function filterReasonPhrase($code, $reasonPhrase)
    {
        return $reasonPhrase === '' ? $this->phrases[$code] : $reasonPhrase;
    }
}