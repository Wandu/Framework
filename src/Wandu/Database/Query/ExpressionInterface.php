<?php
namespace Wandu\Database\Query;

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
