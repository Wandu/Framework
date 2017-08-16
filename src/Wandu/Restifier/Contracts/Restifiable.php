<?php
namespace Wandu\Restifier\Contracts;

interface Restifiable
{
    /**
     * @param mixed $resource
     * @param array $includes
     * @return array|null
     */
    public function restify($resource, array $includes = []);
    
    /**
     * @param array|\Traversable $resources
     * @param array $includes
     * @return array
     */
    public function restifyMany($resources, array $includes): array;
}
