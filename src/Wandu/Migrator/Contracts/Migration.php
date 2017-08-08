<?php
namespace Wandu\Migrator\Contracts;

interface Migration
{
    public function up();
    
    public function down();
}
