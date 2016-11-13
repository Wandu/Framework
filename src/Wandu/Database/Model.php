<?php
namespace Wandu\Database;

use ArrayAccess;
use Wandu\Database\Contracts\ModelInterface;

class Model implements ArrayAccess, ModelInterface
{
    public static function fromRepository()
    {
        // TODO: Implement fromStorage() method.
    }

    public function toRepository()
    {
        // TODO: Implement toStorage() method.
    }

    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset($offset)
    {
        $this->{$offset} = null;
    }
}
