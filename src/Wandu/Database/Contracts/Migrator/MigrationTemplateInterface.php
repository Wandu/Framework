<?php
namespace Wandu\Database\Contracts\Migrator;

interface MigrationTemplateInterface
{
    /**
     * @param string $migrateName
     * @return string
     */
    public function template($migrateName);
}
