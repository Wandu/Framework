<?php
namespace Wandu\Database\Query\Expression;

use PHPUnit_Framework_TestCase;
use Wandu\Collection\ArrayList;
use Wandu\Database\Query\SelectQuery;

class WhereExpressionTest extends PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $query = new WhereExpression();
        
        static::assertEquals('', $query->toSql());
        static::assertEquals([], $query->getBindings());
    }

    public function testWhereOnlyOne()
    {
        $query = new WhereExpression();
        $query->where('foo', '=', 'foo string');

        static::assertEquals('WHERE `foo` = ?', $query->toSql());
        static::assertEquals(['foo string'], $query->getBindings());

        $query = new WhereExpression();
        $query->andWhere('foo', '=', 'foo string');

        static::assertEquals('WHERE `foo` = ?', $query->toSql());
        static::assertEquals(['foo string'], $query->getBindings());

        $query = new WhereExpression();
        $query->orWhere('foo', '=', 'foo string');

        static::assertEquals('WHERE `foo` = ?', $query->toSql());
        static::assertEquals(['foo string'], $query->getBindings());
    }

    public function testWhereOnlyOneWithNoOperator()
    {
        $query = new WhereExpression();
        $query->where('foo', 'foo string');

        static::assertEquals('WHERE `foo` = ?', $query->toSql());
        static::assertEquals(['foo string'], $query->getBindings());

        $query = new WhereExpression();
        $query->andWhere('foo', 'foo string');

        static::assertEquals('WHERE `foo` = ?', $query->toSql());
        static::assertEquals(['foo string'], $query->getBindings());

        $query = new WhereExpression();
        $query->orWhere('foo', 'foo string');

        static::assertEquals('WHERE `foo` = ?', $query->toSql());
        static::assertEquals(['foo string'], $query->getBindings());
    }

    public function testWhereChaining()
    {
        $query = new WhereExpression();
        $query->where('foo', 'foo string')->andWhere('bar', '>', 'bar string');

        static::assertEquals('WHERE `foo` = ? AND `bar` > ?', $query->toSql());
        static::assertEquals(['foo string', 'bar string'], $query->getBindings());

        $query = new WhereExpression();
        $query->andWhere('foo', 'foo string')->orWhere('bar', '<', 'bar string');

        static::assertEquals('WHERE `foo` = ? OR `bar` < ?', $query->toSql());
        static::assertEquals(['foo string', 'bar string'], $query->getBindings());

        $query = new WhereExpression();
        $query->orWhere('foo', 'foo string')->where('bar', 'bar string');

        static::assertEquals('WHERE `foo` = ? AND `bar` = ?', $query->toSql());
        static::assertEquals(['foo string', 'bar string'], $query->getBindings());
    }

    public function testWhereArray()
    {
        $query = new WhereExpression();
        $query->where(['foo' => 'foo string', 'bar' => 'bar string']);

        static::assertEquals('WHERE `foo` = ? AND `bar` = ?', $query->toSql());
        static::assertEquals(['foo string', 'bar string'], $query->getBindings());

        $query = new WhereExpression();
        $query->where(['foo' => 'foo string', 'bar' => ['>' => 'bar string']]);

        static::assertEquals('WHERE `foo` = ? AND `bar` > ?', $query->toSql());
        static::assertEquals(['foo string', 'bar string'], $query->getBindings());

        $query = new WhereExpression();
        $query->orWhere(['foo' => 'foo string', 'bar' => ['>' => 'bar string11', '<' => 'bar string22']]);

        static::assertEquals('WHERE `foo` = ? OR (`bar` > ? AND `bar` < ?)', $query->toSql());
        static::assertEquals(['foo string', 'bar string11', 'bar string22'], $query->getBindings());
    }

    public function testClosure()
    {
        $query = new WhereExpression();
        $query->where(function (LogicalExpression $query) {
            $query->where('foo', 'inner foo string');
            $query->orWhere('bar', 'inner bar string');
            return $query;
        })->where('other', '<', 30);

        static::assertEquals('WHERE (`foo` = ? OR `bar` = ?) AND `other` < ?', $query->toSql());
        static::assertEquals(['inner foo string', 'inner bar string', 30], $query->getBindings());

        $query = new WhereExpression();
        $query->where(function (LogicalExpression $query) {
            $query->where('foo', 'inner foo string');
            $query->orWhere(function (LogicalExpression $query) {
                $query->orWhere('bar1', 'inner bar1 string');
                $query->orWhere('bar2', 'inner bar2 string');
            });
            return $query;
        })->where('other', '<', 30);

        static::assertEquals('WHERE (`foo` = ? OR (`bar1` = ? OR `bar2` = ?)) AND `other` < ?', $query->toSql());
        static::assertEquals(['inner foo string', 'inner bar1 string', 'inner bar2 string', 30], $query->getBindings());
    }

    public function testSubQuery()
    {
        $query = new WhereExpression();
        $query->where("id", "in", (new SelectQuery("users", ["user_id"]))->where("created_at", ">", "2017-05-05"));

        static::assertEquals('WHERE `id` IN (SELECT `user_id` FROM `users` WHERE `created_at` > ?)', $query->toSql());
        static::assertEquals(["2017-05-05"], $query->getBindings());
    }

    public function testWhereInTraversable()
    {
        $query = new WhereExpression();
        $query->where("id", "in", new ArrayList([30, 40, 50]));

        static::assertEquals('WHERE `id` IN (?, ?, ?)', $query->toSql());
        static::assertEquals([30, 40, 50], $query->getBindings());
    }

    public function testWhereWithRawQuery()
    {
        $query = new WhereExpression();
        $query->where(function (LogicalExpression $query) {
            $query->where('foo', 'inner foo string');
            $query->orWhere(function (LogicalExpression $query) {
                $query->orWhere('created_at', '>', new RawExpression("FROM_UNIXTIME(?, ?)", [1496043752, "Y-m-d H:i:s"]));
                $query->orWhere('bar2', 'inner bar2 string');
            });
            return $query;
        })->where('other', '<', 30);

        static::assertEquals('WHERE (`foo` = ? OR (`created_at` > FROM_UNIXTIME(?, ?) OR `bar2` = ?)) AND `other` < ?', $query->toSql());
        static::assertEquals(['inner foo string', 1496043752, 'Y-m-d H:i:s', 'inner bar2 string', 30], $query->getBindings());

        $query = new WhereExpression();
        $query->where(function (LogicalExpression $query) {
            $query->where('foo', 'inner foo string');
            $query->orWhere(function (LogicalExpression $query) {
                $query->orWhere(new RawExpression("FROM_UNIXTIME(?, ?) = 1", [1496043752, "Y-m-d H:i:s"]));
                $query->orWhere('bar2', 'inner bar2 string');
            });
            return $query;
        })->where('other', '<', 30);

        static::assertEquals('WHERE (`foo` = ? OR (FROM_UNIXTIME(?, ?) = 1 OR `bar2` = ?)) AND `other` < ?', $query->toSql());
        static::assertEquals(['inner foo string', 1496043752, 'Y-m-d H:i:s', 'inner bar2 string', 30], $query->getBindings());
    }
}
