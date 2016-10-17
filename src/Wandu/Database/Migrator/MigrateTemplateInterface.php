<?php
namespace Wandu\Database\Migrator;

interface MigrateTemplateInterface
{
    /**
     * @param string $migrateName
     * @return string
     */
    public function getContext($migrateName);
}
