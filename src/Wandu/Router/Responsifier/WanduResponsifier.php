<?php
namespace Wandu\Router\Responsifier;

use Generator;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Wandu\Router\Contracts\ResponsifierInterface;
use function Wandu\Http\response;

class WanduResponsifier implements ResponsifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function responsify($response)
    {
        if ($response instanceof ResponseInterface) {
            return $response;
        }
        if (!isset($response)) {
            $response = '';
        }
        while (is_callable($response)) {
            $nextResponse = call_user_func($response);
            $response = $nextResponse;
        }
        // int, float, boolean, string
        if (is_scalar($response)) {
            if ($response === true) {
                $response = 'true';
            } elseif ($response === false) {
                $response = 'false';
            }
            return response()->create((string)$response);
        }
        if ($response instanceof Generator) {
            return response()->generator($response);
        }
        if (is_array($response) || is_object($response)) {
            return response()->json($response);
        }
        if (is_resource($response)) {
            if ('stream' === get_resource_type($response)) {
                $mode = stream_get_meta_data($response)['mode'];
                // @todo use Stream
                if (strpos($mode, 'r') !== false || strpos($mode, '+') !== false) {
                    $contents = '';
                    while (!feof($response)) {
                        $contents .= fread($response, 1024);
                    }
                    return response()->create($contents);
                }
            }
        }

        throw new RuntimeException('Unsupported Type of Response.');
    }
}
