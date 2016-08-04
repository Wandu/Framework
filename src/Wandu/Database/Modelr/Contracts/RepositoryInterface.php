<?php
namespace Wandu\Database\Modelr\Contracts;

interface RepositoryInterface
{
    /**
     * @return int
     */
    public function count();

    /**
     * @param string $identifier
     * @return \Wandu\Database\Modelr\Contracts\ModelInterface
     */
    public function getItem($identifier);

    /**
     * @param array $identifiers
     * @return \Wandu\Database\Modelr\Contracts\CollectionInterface
     */
    public function getItems(array $identifiers = []);

    /**
     * @param string $identifier
     * @param array $dataSet
     * @return \Wandu\Database\Modelr\Contracts\ModelInterface
     */
    public function updateItem($identifier, array $dataSet);

    /**
     * @param array $dataSet
     * @return \Wandu\Database\Modelr\Contracts\ModelInterface
     */
    public function createItem(array $dataSet);

    /**
     * @param array $items
     */
    public function insertItems(array $items);

    /**
     * @param string $identifier
     */
    public function deleteItem($identifier);
}
