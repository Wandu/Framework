<?php
namespace Wandu\Database\Query;

use PHPUnit_Framework_TestCase;

class QueryBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testSelect()
    {
        $builder = new QueryBuilder('hello');

        static::assertEquals('SELECT * FROM `hello`', $builder->toSql());
        static::assertEquals([], $builder->getBindings());
    }

    public function testSelectWithWhere()
    {
        $builder = QueryBuilder::create('hello')->where(['foo' => ['<' => ['foo string']]])->where('bar', 'bar string')->orWhere('def', 'def');

        static::assertEquals('SELECT * FROM `hello` WHERE `foo` < ? AND `bar` = ? OR `def` = ?', $builder->toSql());
        static::assertEquals(['foo string', 'bar string', 'def'], $builder->getBindings());
    }

    public function testSelectComplex()
    {
        $builder = QueryBuilder::create('hello')
            ->where(['foo' => ['<' => ['foo string']]])
            ->where('bar', 'bar string')
            ->orWhere('def', 'def')
            ->orderBy('id', false)
            ->take(10)
            ->offset(20);

        static::assertEquals('SELECT * FROM `hello` WHERE `foo` < ? AND `bar` = ? OR `def` = ? ORDER BY `id` DESC LIMIT ? OFFSET ?', $builder->toSql());
        static::assertEquals(['foo string', 'bar string', 'def', 10, 20], $builder->getBindings());
    }

    public function testInsertOne()
    {
        $builder = QueryBuilder::create('hello')->insert([
            'foo' => 'foo string',
            'bar' => 'bar string',
        ]);

        static::assertEquals('INSERT INTO `hello`(`foo`, `bar`) VALUES (?, ?)', $builder->toSql());
        static::assertEquals(['foo string', 'bar string'], $builder->getBindings());

        $builder = QueryBuilder::create('hello')->insert([
            [
                'foo' => 'foo string',
                'bar' => 'bar string',
            ]
        ]);

        static::assertEquals('INSERT INTO `hello`(`foo`, `bar`) VALUES (?, ?)', $builder->toSql());
        static::assertEquals(['foo string', 'bar string'], $builder->getBindings());
    }

    public function testInsertMany()
    {
        $builder = QueryBuilder::create('hello')->insert([
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

        static::assertEquals('INSERT INTO `hello`(`id`, `foo`, `bar`) VALUES (?, ?, ?), (?, ?, ?)', $builder->toSql());
        static::assertEquals([3, 'foo string', 'bar string', 5, null, 'bar string22',], $builder->getBindings());
    }

    public function testUpdate()
    {
        $builder = QueryBuilder::create('hello')->update([
            'foo' => 'foo string',
            'bar' => 'bar string',
        ]);

        static::assertEquals('UPDATE `hello` SET `foo` = ?, `bar` = ?', $builder->toSql());
        static::assertEquals(['foo string', 'bar string'], $builder->getBindings());
    }

    public function testDelete()
    {
        $builder = QueryBuilder::create('hello')->delete();

        static::assertEquals('DELETE FROM `hello`', $builder->toSql());
        static::assertEquals([], $builder->getBindings());
    }
}
