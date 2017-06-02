<?php
namespace Wandu\Database\Query\Expression;

use PHPUnit\Framework\TestCase;

class OrderByExpressionTest extends TestCase
{
    public function testEmpty()
    {
        $query = new OrderByExpression();

        static::assertEquals('', $query->toSql());
        static::assertEquals([], $query->getBindings());
    }

    public function testOrderByOnlyOne()
    {
        $query = new OrderByExpression();

        $query->orderBy('id');

        static::assertEquals('ORDER BY `id`', $query->toSql());
        static::assertEquals([], $query->getBindings());

        $query = new OrderByExpression();

        $query->orderBy('id', true);

        static::assertEquals('ORDER BY `id`', $query->toSql());
        static::assertEquals([], $query->getBindings());

        $query = new OrderByExpression();

        $query->orderBy('id', false);

        static::assertEquals('ORDER BY `id` DESC', $query->toSql());
        static::assertEquals([], $query->getBindings());
    }

    public function testAndOrderByOnlyOne()
    {
        $query = new OrderByExpression();

        $query->andOrderBy('id');

        static::assertEquals('ORDER BY `id`', $query->toSql());
        static::assertEquals([], $query->getBindings());

        $query = new OrderByExpression();

        $query->andOrderBy('id', true);

        static::assertEquals('ORDER BY `id`', $query->toSql());
        static::assertEquals([], $query->getBindings());

        $query = new OrderByExpression();

        $query->andOrderBy('id', false);

        static::assertEquals('ORDER BY `id` DESC', $query->toSql());
        static::assertEquals([], $query->getBindings());
    }

    public function testMany()
    {
        $query = new OrderByExpression();

        $query->orderBy('id')->orderBy('updated_at', false);

        // replaced
        static::assertEquals('ORDER BY `updated_at` DESC', $query->toSql());
        static::assertEquals([], $query->getBindings());

        $query = new OrderByExpression();

        $query->orderBy('id', false)->orderBy('updated_at')->orderBy('phone', true);

        static::assertEquals('ORDER BY `phone`', $query->toSql());
        static::assertEquals([], $query->getBindings());
    }

    public function testOrderByWithAndOrderBy()
    {
        $query = new OrderByExpression();

        $query->orderBy('id')->andOrderBy('updated_at', false);

        static::assertEquals('ORDER BY `id`, `updated_at` DESC', $query->toSql());
        static::assertEquals([], $query->getBindings());

        $query = new OrderByExpression();

        $query->orderBy('id', false)->andOrderBy('updated_at')->andOrderBy('phone', true);

        static::assertEquals('ORDER BY `id` DESC, `updated_at`, `phone`', $query->toSql());
        static::assertEquals([], $query->getBindings());
    }

    public function testManyByArray()
    {
        $query = new OrderByExpression();

        $query->orderBy(['id' => true, 'updated_at' => false])->andOrderBy('id', false);

        static::assertEquals('ORDER BY `id`, `updated_at` DESC, `id` DESC', $query->toSql());
        static::assertEquals([], $query->getBindings());
    }
}
