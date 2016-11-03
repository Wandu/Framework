<?php
namespace Wandu\Database;

use Doctrine\Common\Annotations\AnnotationReader;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\Identifier;

class RepositoryTest extends SakilaTestCase
{
    public function testAll()
    {
        $expectedModels = [
            new Actor(183, 'RUSSELL', 'CLOSE', '2006-02-15 04:34:33'),
            new Actor(181, 'MATTHEW', 'CARREY', '2006-02-15 04:34:33'),
            new Actor(176, 'JON', 'CHASE', '2006-02-15 04:34:33'),
        ];
        
        $repository = new Repository($this->connection, new AnnotationReader(), Actor::class);
        $interateCount = 0;
        foreach ($repository->fetch("SELECT * FROM `actor` WHERE `last_name` LIKE ? ORDER BY `actor_id` DESC LIMIT 3", ["C%"]) as $index => $model) {
            $interateCount++;
            static::assertNotSame($expectedModels[$index], $model);
            static::assertEquals($expectedModels[$index], $model);
        }
        static::assertEquals(3, $interateCount);
    }
}

class Actor
{
    /**
     * @Identifier
     * @Column(name="actor_id", cast="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(name="first_name")
     * @var string
     */
    private $firstName;

    /**
     * @Column(name="last_name")
     * @var string
     */
    private $lastName;

    /**
     * @Column(name="last_update")
     * @var string
     */
    private $lastUpdate;

    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     * @param string $lastUpdate
     */
    public function __construct($id, $firstName, $lastName, $lastUpdate)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->lastUpdate = $lastUpdate;
    }
}
