<?php
namespace Wandu\Database\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Column
{
    /**
     * @Required
     * @var string
     */
    public $name;
}
