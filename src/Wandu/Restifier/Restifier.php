<?php
namespace Wandu\Restifier;

use Wandu\Restifier\Contracts\TransformResource;
use Traversable;

class Restifier
{
    /**
     * @param \Wandu\Restifier\Contracts\TransformResource|array|\Traversable $resource
     * @param array $includes
     * @return array
     */
    public function transform($resource, array $includes = [])
    {
        if ($resource instanceof TransformResource) {
            return $this->transformSingle($resource, $includes);
        }
        // iterator or Traversable
        return $this->transformCollection($resource, $includes);
    }
    
    private function transformSingle(TransformResource $resource, $includes = [])
    {
        $entity = $resource->transform();
        if (!is_array($entity)) return $entity;

        // now, result is always array
        foreach ($this->parseIncludes($resource, $includes) as $key => $nextIncludes) {
            $entity = array_merge(
                $entity,
                $this->transformCollection($resource->includeAttribute($key) ?: [], $nextIncludes)
            );
        }
        return $entity;
    }

    /**
     * @param array|\Traversable $resources
     * @param array $includes
     * @return array
     */
    private function transformCollection($resources, array $includes = [])
    {
        $result = [];
        foreach ($resources as $key => $resource) {
            if (is_array($resource) || $resource instanceof Traversable) {
                $result[$key] = $this->transformCollection($resource, $includes);
            } elseif ($resource instanceof TransformResource) {
                $result[$key] = $this->transformSingle($resource, $includes);
            } else {
                $result[$key] = $resource;
            }
        }
        return $result;
    }

    /**
     * @param \Wandu\Restifier\Contracts\TransformResource $origin
     * @param array $includes
     * @return array
     */
    private function parseIncludes(TransformResource $origin, array $includes = [])
    {
        $parsedIncludes = [];
        foreach ($includes as $include => $condition) {
            if (is_integer($include)) {
                $include = $condition;
                $condition = true;
            }
            while (is_callable($condition)) {
                $condition = call_user_func($condition, $origin);
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
