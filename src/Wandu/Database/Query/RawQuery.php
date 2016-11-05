<?php
namespace Wandu\Database\Query;

use Wandu\Database\Contracts\QueryInterface;

class RawQuery implements QueryInterface
{
    /** @var string */
    protected $query;

    /** @var array */
    protected $bindings;

    /**
     * @param string $query
     * @param array $bindings
     */
    public function __construct($query, array $bindings = [])
    {
        $this->query = $query;
        $this->bindings = $bindings;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }
}
