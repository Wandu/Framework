<?php
namespace Wandu\Database\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Wandu\Database\DatabaseManager;
use Wandu\Database\Query\SelectQuery;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class HasOne implements RelationInterface
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
    public function getRelation(DatabaseManager $manager, $columnValue)
    {
        return $manager->repository($this->related)->first(function (SelectQuery $query) use ($columnValue) {
            return $query->where($this->key, $columnValue);
        });
    }
}
