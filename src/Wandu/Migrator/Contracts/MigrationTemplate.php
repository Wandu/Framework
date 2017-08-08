<?php
namespace Wandu\Migrator\Contracts;

interface MigrationTemplate
{
    /**
     * @param string $migrateName
     * @return string
     */
    public function template($migrateName);
}
