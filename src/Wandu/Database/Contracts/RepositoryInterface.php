<?php
namespace Wandu\Database\Contracts;

interface RepositoryInterface
{
    /**
     * @return int
     */
    public function countAll();

    /**
     * @param array|\Traversable $items
     */
    public function insertItems($items);

    /**
     * {@inheritdoc}
     */
    public function findItems(array $where);

    /**
     * {@inheritdoc}
     */
    public function getAllItems();

    /**
     * {@inheritdoc}
     */
    public function getItemsById(array $arrayOfId);

    /**
     * {@inheritdoc}
     */
    public function getItem($id);

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, array $dataSet);

    /**
     * {@inheritdoc}
     */
    public function createItem(array $dataSet);

    /**
     * {@inheritdoc}
     */
    public function deleteItem($id);
}
