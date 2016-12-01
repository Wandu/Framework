<?php
namespace Wandu\Collection\Contracts;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Serializable;

interface SetInterface extends ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Serializable
{
    /**
     * @param \Wandu\Collection\Contracts\SetInterface $other
     * @return bool
     */
    public function equal(SetInterface $other);

    /**
     * @param \Wandu\Collection\Contracts\SetInterface $other
     * @return \Wandu\Collection\Contracts\SetInterface
     */
    public function intersect(SetInterface $other);

    /**
     * @param \Wandu\Collection\Contracts\SetInterface $other
     * @return \Wandu\Collection\Contracts\SetInterface
     */
    public function union(SetInterface $other);

    /**
     * @param \Wandu\Collection\Contracts\SetInterface $other
     * @return \Wandu\Collection\Contracts\SetInterface
     */
    public function diff(SetInterface $other);

    /**
     * @param mixed $item
     * @return bool
     */
    public function has($item);

    /**
     * @param mixed $item
     */
    public function add($item);

    /**
     * @param mixed $item
     */
    public function remove($item);
}
