<?php
namespace Wandu\Restifier\Contracts;

interface Restifiable
{
    /**
     * @param mixed $resource
     * @param array $includes
     * @return array
     */
    public function restify($resource, array $includes = []): array;
    
    /**
     * @param array|\Traversable $resources
     * @param array $includes
     * @return array
     */
    public function restifyMany($resources, array $includes): array;
}
