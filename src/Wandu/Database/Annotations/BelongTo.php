<?php
namespace Wandu\Database\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Wandu\Database\Manager;
use Wandu\Database\Query\SelectQuery;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class BelongTo implements RelationInterface
{
    /**
     * @Required
     * @var string
     */
    public $related;

    /**
     * @var string
     */
    public $key = 'id';

    /**
     * {@inheritdoc}
     */
    public function getRelation(Manager $manager, $columnValue)
    {
        return $manager->repository($this->related)->first(function (SelectQuery $query) use ($columnValue) {
            return $query->where($this->key, $columnValue);
        });
    }
}
