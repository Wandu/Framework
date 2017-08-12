<?php
namespace Wandu\Restifier;

use InvalidArgumentException;
use Wandu\Restifier\Contracts\Restifiable;

class Restifier implements Restifiable
{
    /** @var callable[] */
    protected $transformers = [];
    
    public function __construct(array $transformers = [])
    {
        foreach ($transformers as $name => $transformer) {
            $this->addTransformer($name, $transformer);
        }
    }

    /**
     * @param string $name
     * @param callable $transformer
     */
    public function addTransformer(string $name, callable $transformer)
    {
        if (is_callable($transformer)) {
            $this->transformers[$name] = $transformer;
            return;
        }
        throw new InvalidArgumentException("Argument 2 passed to pushTransformer() must be callable");
    }
    
    /**
     * @param mixed $resource
     * @param array $includes
     * @return array
     */
    public function restify($resource, array $includes = []): array
    {
        $transformer = $this->transformers[get_class($resource)];
        $parsedIncludes = $this->parseIncludes($includes, $resource);
        $entity = call_user_func($transformer, $resource, $this, $parsedIncludes);
        foreach ($parsedIncludes as $key => $nextIncludes) {
            if (is_object($transformer) && method_exists($transformer, $key)) {
                $entity = array_merge(
                    $entity,
                    $transformer->{$key}($resource, $this, $nextIncludes)
                );
            }
        }
        return $entity;
    }

    /**
     * @param array|\Traversable $resource
     * @param array $includes
     * @return array
     */
    public function restifyMany($resource, array $includes = []): array
    {
        $result = [];
        foreach ($resource as $key => $value) {
            $result[$key] = $this->restify($value, $includes);
        }
        return $result;
    }

    /**
     * @param array $includes
     * @param mixed $resource
     * @return array
     */
    private function parseIncludes(array $includes = [], $resource)
    {
        $parsedIncludes = [];
        foreach ($includes as $include => $condition) {
            if (is_integer($include)) {
                $include = $condition;
                $condition = true;
            }
            while (is_callable($condition)) {
                $condition = call_user_func($condition, $resource);
            }
            if (!$condition) continue;
            if (strpos($include, '.') === false) {
                $key = $include;
                $param = null;
            } else {
                list($key, $param) = explode('.', $include, 2);
            }
            if (!isset($parsedIncludes[$key])) {
                $parsedIncludes[$key] = [];
            }
            if ($param) {
                $parsedIncludes[$key][] = $param;
            }
        }
        return $parsedIncludes;
    }
}
