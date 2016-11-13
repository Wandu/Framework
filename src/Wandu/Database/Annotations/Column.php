<?php
namespace Wandu\Database\Annotations;

use Doctrine\Common\Annotations\Annotation\Enum;
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

    /**
     * @Enum({"string", "integer", "float", "boolean", "array", "datetime", "date", "time"})
     * @var string
     */
    public $cast = 'string';
}
