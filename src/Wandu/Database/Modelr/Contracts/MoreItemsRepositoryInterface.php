<?php
namespace Wandu\Modelr\Contracts;

interface MoreItemsRepositoryInterface extends RepositoryInterface
{
    /**
     * @return \Wandu\Modelr\Contracts\ModelInterface
     */
    public function getFirstItem();
    
    /**
     * @param string $itemId
     * @param int $length
     * @return \Wandu\Modelr\Contracts\CollectionInterface
     */
    public function getNextItems($itemId, $length = 10);

    /**
     * @param string $itemId
     * @param int $length
     * @return \Wandu\Modelr\Contracts\CollectionInterface
     */
    public function getPrevItems($itemId, $length = 10);
}
