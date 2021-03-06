<?php
namespace Wandu\Database\Events;

class ExecuteQuery
{
    /** @var string */
    protected $sql;
    
    /** @var array */
    protected $bindings = [];
    
    public function __construct(string $sql, array $bindings = [])
    {
        $this->sql = $sql;
        $this->bindings = $bindings;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }
}
