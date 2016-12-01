<?php
namespace Wandu\Database;

use Doctrine\Common\Annotations\Reader;
use Generator;
use Wandu\Database\Exception\ClassNotFoundException;

class ConnectionTest extends SakilaTestCase
{
    public function testOne()
    {
        $expectedRow = [
            'actor_id' => 183,
            'first_name' => 'RUSSELL',
            'last_name' => 'CLOSE',
            'last_update' => '2006-02-15 04:34:33',
        ];

        $row = $this->connection->first(
            "SELECT * FROM `actor` WHERE `last_name` LIKE ? ORDER BY `actor_id` DESC LIMIT 3",
            ['C%']
        );
        static::assertEquals($expectedRow, $row);

        $row = $this->connection->first(function () {
            return "SELECT * FROM `actor` WHERE `last_name` LIKE ? ORDER BY `actor_id` DESC LIMIT 3"; 
        }, ['C%']);
        static::assertEquals($expectedRow, $row);
    }

    public function testFetch()
    {
        $expectedRows = [
            [
                'actor_id' => 183,
                'first_name' => 'RUSSELL',
                'last_name' => 'CLOSE',
                'last_update' => '2006-02-15 04:34:33',
            ],
            [
                'actor_id' => 181,
                'first_name' => 'MATTHEW',
                'last_name' => 'CARREY',
                'last_update' => '2006-02-15 04:34:33',
            ],
            [
                'actor_id' => 176,
                'first_name' => 'JON',
                'last_name' => 'CHASE',
                'last_update' => '2006-02-15 04:34:33',
            ],
        ];

        $cursor = $this->connection->fetch(
            "SELECT * FROM `actor` WHERE `last_name` LIKE ? ORDER BY `actor_id` DESC LIMIT 3",
            ['C%']
        );
        $interateCount = 0;
        static::assertInstanceOf(Generator::class, $cursor); // cursor is generator!
        foreach ($cursor as $index => $row) {
            // hhvm return 'actor_id' => '176' (string), but php return 'actor_id' => 176 (int)
            static::assertEquals($expectedRows[$index], $row);
            $interateCount++;
        }
        static::assertEquals(3, $interateCount);

        // same
        $cursor = $this->connection->fetch(function () {
            return "SELECT * FROM `actor` WHERE `last_name` LIKE ? ORDER BY `actor_id` DESC LIMIT 3";
        }, ['C%']);
        $interateCount = 0;
        static::assertInstanceOf(Generator::class, $cursor); // cursor is generator!
        foreach ($cursor as $index => $row) {
            static::assertEquals($expectedRows[$index], $row);
            $interateCount++;
        }
        static::assertEquals(3, $interateCount);
    }
    
    public function testFailToCreateRepository()
    {
        $connection = $this->connector->connect();
        try {
            $connection->createRepository('anything');
            static::fail();
        } catch (ClassNotFoundException $e) {
            static::assertEquals(Reader::class, $e->getClassName());
            static::assertContains('Class \'Doctrine\Common\Annotations\Reader\' not found in', $e->getMessage());
        }
    }
}
