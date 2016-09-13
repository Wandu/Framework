<?php
namespace Wandu\Database\Schema;

use Wandu\Database\Schema\Expression\CreateExpression;

class SchemaBuilder
{
    const TYPE_ALTER = 2; // add column / drop column / modify column(rename column) / drop constraint / add constraint / 
    const TYPE_RENAME = 3; //  
    const TYPE_DROP = 4;
    const TYPE_TRUNCATE = 5;

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
}
