<?php
namespace Wandu\Database\Query\Expression;

use Wandu\Database\Contracts\ExpressionInterface;
use Wandu\Database\Support\Helper;

/**
 * OrderByItem = '`' name '`' 'DESC' ? | OrderByItem ', ' OrderByItem
 * OrderByExpression = 'ORDER BY ' OrderByItem
 *
 * @example ORDER BY `id`
 * @example ORDER BY `id` DESC
 * @example ORDER BY `id`, `user_id` DESC
 */
class OrderByExpression implements ExpressionInterface
{
    /** @var array */
    protected $orders = [];

    /**
     * @param string|array $name
     * @param bool $asc
     * @return static
     */
    public function orderBy($name, $asc = true)
    {
        $this->orders = [];
        return $this->andOrderBy($name, $asc);
    }

    /**
     * @param string|array $name
     * @param bool $asc
     * @return static
     */
    public function andOrderBy($name, $asc = true)
    {
        if (is_array($name)) {
            foreach ($name as $key => $asc) {
                $this->orders[] = [$key, $asc];
            }
        } else {
            $this->orders[] = [$name, $asc];
        }
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function toSql()
    {
        if (count($this->orders) === 0) {
            return '';
        }
        $parts = [];
        foreach ($this->orders as $order) {
            $parts[] = Helper::normalizeName($order[0]) . ($order[1] ? '' : ' DESC');
        }
        return 'ORDER BY ' . Helper::arrayImplode(", ", $parts);
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return [];
    }
}
