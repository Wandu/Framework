<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Assign
{
    /** @Required @var string */
    public $name;
    
    /** @Required @var string */
    public $target;
}
