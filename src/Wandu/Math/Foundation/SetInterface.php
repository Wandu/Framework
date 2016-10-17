<?php
namespace Wandu\Math\Foundation;

use Countable;
use IteratorAggregate;

interface SetInterface extends Countable, IteratorAggregate
{
    /**
     * @param \Wandu\Math\Foundation\SetInterface $other
     * @return bool
     */
    public function equal(SetInterface $other);

    /**
     * @param \Wandu\Math\Foundation\SetInterface $other
     * @return \Wandu\Math\Foundation\SetInterface
     */
    public function intersection(SetInterface $other);

    /**
     * @param \Wandu\Math\Foundation\SetInterface $other
     * @return \Wandu\Math\Foundation\SetInterface
     */
    public function union(SetInterface $other);

    /**
     * @param \Wandu\Math\Foundation\SetInterface $other
     * @return \Wandu\Math\Foundation\SetInterface
     */
    public function difference(SetInterface $other);

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
