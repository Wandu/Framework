<?php
namespace Wandu\Database\Annotations;

use Wandu\Database\DatabaseManager;

interface RelationInterface
{
    /**
     * @param \Wandu\Database\DatabaseManager $manager
     * @param mixed $columnValue
     * @return object
     */
    public function getRelation(DatabaseManager $manager, $columnValue);
}
