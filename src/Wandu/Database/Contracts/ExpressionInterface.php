<?php
namespace Wandu\Database\Contracts;

interface ExpressionInterface
{
    /**
     * @return string
     */
    public function toSql();

    /**
     * @return array
     */
    public function getBindings();
}
