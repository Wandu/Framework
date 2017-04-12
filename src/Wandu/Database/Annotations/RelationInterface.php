<?php
namespace Wandu\Database\Annotations;

use Wandu\Database\Manager;

interface RelationInterface
{
    /**
     * @param \Wandu\Database\Manager $manager
     * @param mixed $columnValue
     * @return object
     */
    public function getRelation(Manager $manager, $columnValue);
}
