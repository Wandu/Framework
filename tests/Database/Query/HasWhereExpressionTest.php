<?php
namespace Wandu\Database\Query;

use PHPUnit_Framework_TestCase;

class HasWhereExpressionTest extends PHPUnit_Framework_TestCase
{
    public function testNothing()
    {
        $builder = (new HasWhereExpression);

        static::assertEquals('', $builder->toSql());
        static::assertEquals([], $builder->getBindings());
    }

    public function testWhereOnly()
    {
        $builder = (new HasWhereExpression)->where(['foo' => ['<' => ['foo string']]])->where('bar', 'bar string')->orWhere('def', 'def');

        static::assertEquals('WHERE `foo` < ? AND `bar` = ? OR `def` = ?', $builder->toSql());
        static::assertEquals(['foo string', 'bar string', 'def'], $builder->getBindings());
    }

    public function testOrderByOnly()
    {
        $builder = (new HasWhereExpression)
            ->orderBy('id', false)
            ->andOrderBy('created_at', true);

        static::assertEquals('ORDER BY `id` DESC, `created_at`', $builder->toSql());
        static::assertEquals([], $builder->getBindings());
    }

    public function testLimitOnly()
    {
        $builder = (new HasWhereExpression)
            ->take(10)
            ->offset(20);

        static::assertEquals('LIMIT ? OFFSET ?', $builder->toSql());
        static::assertEquals([10, 20], $builder->getBindings());
    }

    public function testWhereComplex()
    {
        $builder = (new HasWhereExpression)
            ->where(['foo' => ['<' => ['foo string']]])
            ->where('bar', 'bar string')
            ->orWhere('def', 'def')
            ->orderBy('id', false)
            ->take(10)
            ->offset(20);

        static::assertEquals('WHERE `foo` < ? AND `bar` = ? OR `def` = ? ORDER BY `id` DESC LIMIT ? OFFSET ?', $builder->toSql());
        static::assertEquals(['foo string', 'bar string', 'def', 10, 20], $builder->getBindings());
    }
}
