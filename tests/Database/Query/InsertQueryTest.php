<?php
namespace Wandu\Database\Query;

use PHPUnit\Framework\TestCase;
use Wandu\Database\Query\Expression\RawExpression;

class InsertQueryTest extends TestCase 
{
    public function testInsertOne()
    {
        $query = new InsertQuery("example", [
            "name" => "wani",
            "created_at" => "2017-05-29 00:00:00",
        ]);
        static::assertSame("INSERT INTO `example`(`name`, `created_at`) VALUES (?, ?)", $query->toSql());
        static::assertSame(["wani", "2017-05-29 00:00:00"], $query->getBindings());
    }

    public function testInsertMany()
    {
        $query = new InsertQuery("example", [
            [
                "name" => "wani",
                "created_at" => "2017-05-29 00:00:00",
            ],
            [
                "name" => "others",
                "created_at" => "2017-05-30 00:00:00",
            ],
        ]);
        static::assertSame("INSERT INTO `example`(`name`, `created_at`) VALUES (?, ?), (?, ?)", $query->toSql());
        static::assertSame(["wani", "2017-05-29 00:00:00", "others", "2017-05-30 00:00:00"], $query->getBindings());
    }

    public function testInsertManyWithNull()
    {
        $query = new InsertQuery("example", [
            [
                "name" => "wani",
                "created_at" => "2017-05-29 00:00:00",
            ],
            [
                "created_at" => "2017-05-30 00:00:00",
            ],
        ]);
        static::assertSame("INSERT INTO `example`(`name`, `created_at`) VALUES (?, ?), (?, ?)", $query->toSql());
        static::assertSame(["wani", "2017-05-29 00:00:00", null, "2017-05-30 00:00:00"], $query->getBindings());
    }

    public function testInsertWithRawExpression()
    {
        $query = new InsertQuery("example", [
            [
                "name" => "wani",
                "created_at" => new RawExpression("from_unixtime(?, ?)", [1496041501, "Y-m-d H:i:s"]),
            ],
            [
                "name" => "others",
                "created_at" => "2017-05-31 00:00:00",
            ],
        ]);
        static::assertSame("INSERT INTO `example`(`name`, `created_at`) VALUES (?, from_unixtime(?, ?)), (?, ?)", $query->toSql());
        static::assertSame(["wani", 1496041501, "Y-m-d H:i:s", 'others', "2017-05-31 00:00:00"], $query->getBindings());
    }
}
