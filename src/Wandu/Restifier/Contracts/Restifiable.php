<?php
namespace Wandu\Restifier\Contracts;

interface Restifiable
{
    /**
     * @param mixed $resource
     * @param array $includes
     * @param callable $transformer
     * @return array|null
     */
    public function restify($resource, array $includes = [], callable $transformer = null);
    
    /**
     * @param array|\Traversable $resources
     * @param array $includes
     * @param callable $transformer
     * @return array
     */
    public function restifyMany($resources, array $includes = [], callable $transformer = null): array;
}
