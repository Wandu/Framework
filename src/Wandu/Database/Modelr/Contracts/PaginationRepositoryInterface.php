<?php
namespace Wandu\Database\Modelr\Contracts;

interface PaginationRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $skip
     * @param int $take
     * @return \Wandu\Database\Modelr\Contracts\CollectionInterface
     */
    public function getSkippedItems($skip = 0, $take = 10);
}
