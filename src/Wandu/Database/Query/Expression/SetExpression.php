<?php
namespace Wandu\Database\Query\Expression;

use Wandu\Database\Query\ExpressionInterface;
use Wandu\Database\Support\Helper;

class SetExpression implements ExpressionInterface
{
    /** @var array */
    protected $attributes = [];

    /**
     * @param array $attributes
     * @return static
     */
    public function set(array $attributes = [])
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param array $attributes
     * @return static
     */
    public function addSet(array $attributes = [])
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if (count($this->attributes)) {
            $columns = array_keys($this->attributes);
            return "SET " . Helper::arrayImplode(', ', $columns, "`", "` = ?");
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return array_values($this->attributes);
    }
}
