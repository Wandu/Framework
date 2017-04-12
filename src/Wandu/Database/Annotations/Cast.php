<?php
namespace Wandu\Database\Annotations;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Cast
{
    /**
     * @Enum({"string", "integer", "float", "boolean", "array", "datetime", "date", "time"})
     * @var string
     */
    public $type = 'string';
    
    public $caster = '';
}
