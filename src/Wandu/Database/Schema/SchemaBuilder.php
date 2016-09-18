<?php
namespace Wandu\Database\Schema;

use Wandu\Database\Schema\Expression\CreateExpression;
use Wandu\Database\Schema\Expression\DropExpression;
use Wandu\Database\Schema\Expression\RenameExpression;
use Wandu\Database\Schema\Expression\TruncateExpression;

/**
 * @todo ALTER add column / drop column / modify column(rename column) / drop constraint / add constraint
 */
class SchemaBuilder
{
    /** @var string */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param string $table
     * @param callable $defineHandler
     * @return \Wandu\Database\Schema\Expression\CreateExpression
     */
    public function create($table, callable $defineHandler = null)
    {
        return new CreateExpression($table, $defineHandler);
    }

    /**
     * @param string $oldTableName
     * @param string $newTableName
     * @return \Wandu\Database\Schema\Expression\RenameExpression
     */
    public function rename($oldTableName, $newTableName)
    {
        return new RenameExpression($oldTableName, $newTableName);
    }

    /**
     * @param string $table
     * @return \Wandu\Database\Schema\Expression\DropExpression
     */
    public function drop($table)
    {
        return new DropExpression($table);
    }

    /**
     * @param string $table
     * @return \Wandu\Database\Schema\Expression\TruncateExpression
     */
    public function truncate($table)
    {
        return new TruncateExpression($table);
    }
}
