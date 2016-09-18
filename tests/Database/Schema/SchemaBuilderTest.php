<?php
namespace Wandu\Database\Schema;

use PHPUnit_Framework_TestCase;
use Wandu\Database\Query\RawExpression;
use Wandu\Database\Schema\Expression\ConstraintExpression;
use Wandu\Database\Schema\Expression\CreateExpression;
use Wandu\Database\Schema\Expression\ReferenceExpression;

class SchemaBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testCreateOnlyColumns()
    {
        $builder = new SchemaBuilder();
        
        $builder = $builder->create('users', function (CreateExpression $table) {
            $table->bigInteger('id')->unsigned()->primary()->autoIncrement();
            $table->bigInteger('group_id')->nullable()->reference('groups', 'id');
            $table->string('username', 30);
            $table->varchar('password', 30)->nullable();
            $table->varbinary('order', 12)->default('00a.00a.00a');
            $table->blob('profile');
            $table->enum('grant', ['ADMIN', 'CUSTOMER', 'GUEST']);
            $table->timestamp('created_at')->default(new RawExpression('CURRENT_TIMESTAMP'));
            $table->addColumn(new RawExpression('`updated_at` TIMESTAMP DEFAULT now() ON UPDATE now()'));
            
            $table->engine('MyISAM');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
        })->ifNotExists();
        
        static::assertEquals(<<<SQL
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `group_id` BIGINT(20) NULL REFERENCES `groups`(`id`),
  `username` VARCHAR(30) NOT NULL,
  `password` VARCHAR(30) NULL,
  `order` VARBINARY(12) NOT NULL DEFAULT '00a.00a.00a',
  `profile` BLOB NOT NULL,
  `grant` ENUM('ADMIN', 'CUSTOMER', 'GUEST') NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT now() ON UPDATE now()
) ENGINE=MyISAM CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL
            ,
            $builder->toSql()
        );
    }

    public function testCreateWithConstraints()
    {
        $builder = new SchemaBuilder();

        $builder = $builder->create('articles', function (CreateExpression $table) {
            $table->bigInteger('id')->unsigned()->autoIncrement();
            $table->bigInteger('user_id')->nullable();
            $table->string('category', 20);
            $table->string('title', 100);
            $table->longText('contents');
            $table->timestamp('created_at')->default(new RawExpression('CURRENT_TIMESTAMP'));

            $table->primaryKey('id');
            $table->foreignKey('user_id', 'user_foreign_key')
                ->reference('users', 'id')
                ->onUpdate(ReferenceExpression::OPTION_CASCADE)
                ->onDelete(ReferenceExpression::OPTION_SET_NULL);
            $table->index('title')->using(ConstraintExpression::INDEX_TYPE_HASH);
            $table->uniqueKey(['category', 'title']);
            $table->addConstraint(new RawExpression('FULLTEXT INDEX `search_contents` (`contents`)'));

            $table->ifNotExists(); // it also safe :-)
        });

        static::assertEquals(<<<SQL
CREATE TABLE IF NOT EXISTS `articles` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) NULL,
  `category` VARCHAR(20) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `contents` LONGTEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY `user_foreign_key` (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  KEY USING HASH (`title`),
  UNIQUE KEY (`category`, `title`),
  FULLTEXT INDEX `search_contents` (`contents`)
)
SQL
            ,
            $builder->toSql()
        );
    }

    public function testRename()
    {
        $builder = new SchemaBuilder();

        static::assertEquals("RENAME TABLE `users` TO `other_users`", $builder->rename('users', 'other_users')->toSql());
    }

    public function testDrop()
    {
        $builder = new SchemaBuilder();

        static::assertEquals("DROP TABLE IF EXISTS `users`", $builder->drop('users')->ifExists()->toSql());
        static::assertEquals("DROP TABLE `users` RESTRICT", $builder->drop('users')->restrict()->toSql());
        static::assertEquals("DROP TABLE `users` CASCADE", $builder->drop('users')->cascade()->toSql());
    }

    public function testTruncate()
    {
        $builder = new SchemaBuilder();

        static::assertEquals("TRUNCATE TABLE `users`", $builder->truncate('users')->toSql());
    }
}
