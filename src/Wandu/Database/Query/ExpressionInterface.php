<?php
namespace Wandu\Database\Query;

interface ExpressionInterface
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * @return array
     */
    public function getBindings();
}
