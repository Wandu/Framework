<?php
namespace Wandu\Database\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class HasOne
{
    /**
     * @Required
     * @var string
     */
    public $related;

    /**
     * @var string
     */
    public $foreignKey = 'string';

    /**
     * @var string
     */
    public $localKey = 'id';
}
