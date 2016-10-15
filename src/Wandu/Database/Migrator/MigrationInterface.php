<?php
namespace Wandu\Database\Migrator;

interface MigrationInterface
{
    public function up();
    
    public function down();
}
