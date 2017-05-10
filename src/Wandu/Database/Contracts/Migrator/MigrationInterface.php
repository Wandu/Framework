<?php
namespace Wandu\Database\Contracts\Migrator;

interface MigrationInterface
{
    public function up();
    
    public function down();
}
