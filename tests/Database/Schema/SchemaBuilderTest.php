<?php
namespace Wandu\Database\Schema;

use PHPUnit_Framework_TestCase;
use Wandu\Database\Schema\Expression\CreateExpression;

class SchemaBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $builder = new SchemaBuilder();
        
        $builder = $builder->create('users', function (CreateExpression $table) {
            $table->bigInteger('id')->unsigned()->primary()->autoIncrement();
            $table->string('username', 30);
            $table->varchar('password', 30)->nullable();
            $table->varbinary('order', 12)->default('00a.00a.00a');
            $table->blob('profile');
            $table->enum('grant', ['ADMIN', 'CUSTOMER', 'GUEST']);
            $table->timestamp('created_at')->default(new RawExpression('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            
            $table->engine('MyISAM');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
        })->ifNotExists();

        static::assertEquals(
            'CREATE TABLE IF NOT EXISTS `users` (' .
            '`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, ' .
            '`username` VARCHAR(30) NOT NULL, ' .
            '`password` VARCHAR(30) NULL, ' .
            '`order` VARBINARY(12) NOT NULL DEFAULT \'00a.00a.00a\', ' .
            '`profile` BLOB NOT NULL, ' .
            '`grant` ENUM(\'ADMIN\', \'CUSTOMER\', \'GUEST\') NOT NULL, ' . 
            '`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, ' .
            '`updated_at` TIMESTAMP NULL' .
            ') ENGINE=MyISAM CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            $builder->__toString()
        );
    }
}
