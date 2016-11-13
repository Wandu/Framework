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
     * @param string $name
     * @param string $cast
     * @param bool $increments
     * @return static
     */
    public static function create($name, $cast = 'string', $increments = false)
    {
        $self = new static;
        $self->name = $name;
        $self->cast = $cast;
        $self->increments = $increments;
        return $self;
    }
    
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

    /**
     * @var bool
     */
    public $increments = false;
}
