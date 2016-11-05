<?php
namespace Wandu\Database;

use Wandu\Database\Query\CreateQuery;
use Wandu\Database\Query\DeleteQuery;
use Wandu\Database\Query\DropQuery;
use Wandu\Database\Query\InsertQuery;
use Wandu\Database\Query\RenameQuery;
use Wandu\Database\Query\SelectQuery;
use Wandu\Database\Query\TruncateQuery;
use Wandu\Database\Query\UpdateQuery;

/**
 * @todo ALTER add column / drop column / modify column(rename column) / drop constraint / add constraint
 */
class QueryBuilder
{
    /** @var string */
    protected $table;

    /**
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * @param array $columns
     * @return \Wandu\Database\Query\SelectQuery
     */
    public function select(array $columns = ['*'])
    {
        return new SelectQuery($this->table, $columns);
    }

    /**
     * @param array|\Traversable $values
     * @return \Wandu\Database\Query\InsertQuery
     */
    public function insert($values)
    {
        return new InsertQuery($this->table, $values);
    }
    
    /**
     * @param array $attributes
     * @return \Wandu\Database\Query\UpdateQuery
     */
    public function update(array $attributes = [])
    {
        return new UpdateQuery($this->table, $attributes);
    }

    /**
     * @return \Wandu\Database\Query\DeleteQuery
     */
    public function delete()
    {
        return new DeleteQuery($this->table);
    }

    /**
     * @param callable $defineHandler
     * @return \Wandu\Database\Query\CreateQuery
     */
    public function create(callable $defineHandler = null)
    {
        return new CreateQuery($this->table, $defineHandler);
    }

    /**
     * @param string $newTable
     * @return \Wandu\Database\Query\RenameQuery
     */
    public function rename($newTable)
    {
        return new RenameQuery($this->table, $newTable);
    }

    /**
     * @return \Wandu\Database\Query\DropQuery
     */
    public function drop()
    {
        return new DropQuery($this->table);
    }

    /**
     * @return \Wandu\Database\Query\TruncateQuery
     */
    public function truncate()
    {
        return new TruncateQuery($this->table);
    }
}
