<?php
namespace Wandu\Database;

use PHPUnit\Framework\TestCase;
use Wandu\Database\Query\CreateQuery;
use Wandu\Database\Query\Expression\ConstraintExpression;
use Wandu\Database\Query\Expression\ReferenceExpression;
use Wandu\Database\Query\Expression\RawExpression;

class QueryBuilderTest extends TestCase
{
    /** @var \Wandu\Database\QueryBuilder */
    protected $queryBuilder;
    
    public function setUp()
    {
        $this->queryBuilder = new QueryBuilder('tests');
    }
    
    public function testSelect()
    {
        $query = $this->queryBuilder
            ->select(['user_id', 'created_at'])
            ->where(['foo' => ['<' => ['foo string']]])
            ->where('bar', 'bar string')
            ->orWhere('def', 'def')
            ->orderBy('id', false)
            ->take(10)
            ->offset(20);

        static::assertEquals('SELECT `user_id`, `created_at` FROM `tests` WHERE `foo` < ? AND `bar` = ? OR `def` = ? ORDER BY `id` DESC LIMIT ? OFFSET ?', $query->toSql());
        static::assertEquals(['foo string', 'bar string', 'def', 10, 20], $query->getBindings());
    }
    
    public function testSelectFromOtherDatabase()
    {
        $query = (new QueryBuilder('others.tests'))->select(['user_id', 'created_at']);

        static::assertEquals('SELECT `user_id`, `created_at` FROM `others`.`tests`', $query->toSql());
    }

    public function testInsertOne()
    {
        $query = $this->queryBuilder->insert([
            'foo' => 'foo string',
            'bar' => 'bar string',
        ]);

        static::assertEquals('INSERT INTO `tests`(`foo`, `bar`) VALUES (?, ?)', $query->toSql());
        static::assertEquals(['foo string', 'bar string'], $query->getBindings());

        $query = $this->queryBuilder->insert([
            [
                'foo' => 'foo string',
                'bar' => 'bar string',
            ]
        ]);

        static::assertEquals('INSERT INTO `tests`(`foo`, `bar`) VALUES (?, ?)', $query->toSql());
        static::assertEquals(['foo string', 'bar string'], $query->getBindings());
    }

    public function testInsertMany()
    {
        $query = $this->queryBuilder->insert([
            [
                'id' => 3,
                'foo' => 'foo string',
                'bar' => 'bar string',
            ],
            [
                'id' => 5,
                'bar' => 'bar string22',
                'ignore' => 'ignore......................'
            ],
        ]);

        static::assertEquals('INSERT INTO `tests`(`id`, `foo`, `bar`) VALUES (?, ?, ?), (?, ?, ?)', $query->toSql());
        static::assertEquals([3, 'foo string', 'bar string', 5, null, 'bar string22',], $query->getBindings());
    }

    public function testUpdate()
    {
        $query = $this->queryBuilder
            ->update([
                'foo' => 'foo string',
                'bar' => 'bar string',
            ])
            ->where('bar', 'bar string')
            ->orWhere('def', 'def')
            ->orderBy('id', false)
            ->take(10)
            ->offset(20);

        static::assertEquals('UPDATE `tests` SET `foo` = ?, `bar` = ? WHERE `bar` = ? OR `def` = ? ORDER BY `id` DESC LIMIT ? OFFSET ?', $query->toSql());
        static::assertEquals(['foo string', 'bar string', 'bar string', 'def', 10, 20], $query->getBindings());
    }

    public function testDelete()
    {
        $query = $this->queryBuilder
            ->delete()
            ->where('bar', 'bar string')
            ->orWhere('def', 'def')
            ->orderBy('id', false)
            ->take(10)
            ->offset(20);

        static::assertEquals('DELETE FROM `tests` WHERE `bar` = ? OR `def` = ? ORDER BY `id` DESC LIMIT ? OFFSET ?', $query->toSql());
        static::assertEquals(['bar string', 'def', 10, 20], $query->getBindings());
    }

    public function testCreateOnlyColumns()
    {
        $query = $this->queryBuilder->create(function (CreateQuery $table) {
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
CREATE TABLE IF NOT EXISTS `tests` (
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
            $query->toSql()
        );
    }

    public function testCreateWithConstraints()
    {
        $query = $this->queryBuilder->create(function (CreateQuery $table) {
            $table->bigInteger('id')->unsigned()->autoIncrement();
            $table->bigInteger('user_id')->nullable();
            $table->string('category', 20);
            $table->string('title', 100);
            $table->longText('contents');
            $table->timestamp('created_at')->default(new RawExpression('CURRENT_TIMESTAMP'));

            $table->primaryKey('id');
            $table->foreignKey('user_id', 'user_foreign_key')
                ->reference('tests', 'id')
                ->onUpdate(ReferenceExpression::OPTION_CASCADE)
                ->onDelete(ReferenceExpression::OPTION_SET_NULL);
            $table->index('title')->using(ConstraintExpression::INDEX_TYPE_HASH);
            $table->uniqueKey(['category', 'title']);
            $table->addConstraint(new RawExpression('FULLTEXT INDEX `search_contents` (`contents`)'));

            $table->ifNotExists(); // it also safe :-)
        });

        static::assertEquals(<<<SQL
CREATE TABLE IF NOT EXISTS `tests` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) NULL,
  `category` VARCHAR(20) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `contents` LONGTEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY `user_foreign_key` (`user_id`) REFERENCES `tests`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  KEY USING HASH (`title`),
  UNIQUE KEY (`category`, `title`),
  FULLTEXT INDEX `search_contents` (`contents`)
)
SQL
            ,
            $query->toSql()
        );
    }

    public function testRename()
    {
        $query = $this->queryBuilder->rename('other_tests');

        static::assertEquals("RENAME TABLE `tests` TO `other_tests`", $query->toSql());
    }

    public function testDrop()
    {
        static::assertEquals("DROP TABLE IF EXISTS `tests`", $this->queryBuilder->drop()->ifExists()->toSql());
        static::assertEquals("DROP TABLE `tests` RESTRICT", $this->queryBuilder->drop()->restrict()->toSql());
        static::assertEquals("DROP TABLE `tests` CASCADE", $this->queryBuilder->drop()->cascade()->toSql());
    }

    public function testTruncate()
    {
        static::assertEquals("TRUNCATE TABLE `tests`", $this->queryBuilder->truncate()->toSql());
    }
}
