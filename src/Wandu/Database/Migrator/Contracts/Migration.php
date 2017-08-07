<?php
namespace Wandu\Database\Migrator\Contracts;

interface Migration
{
    public function up();
    
    public function down();
}
