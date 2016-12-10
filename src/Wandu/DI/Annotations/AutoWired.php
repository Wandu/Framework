<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class AutoWired
{
    /** @Required @var string */
    public $name;

    /** @var string */
    public $to = null;
}
