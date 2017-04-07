<?php
namespace Wandu\Database\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Table
{
    /**
     * @Required
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * @var bool
     */
    public $increments = true;
}
