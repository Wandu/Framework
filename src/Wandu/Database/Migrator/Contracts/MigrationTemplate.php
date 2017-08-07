<?php
namespace Wandu\Database\Migrator\Contracts;

interface MigrationTemplate
{
    /**
     * @param string $migrateName
     * @return string
     */
    public function template($migrateName);
}
