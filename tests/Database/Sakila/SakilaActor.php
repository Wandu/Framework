<?php
namespace Wandu\Database\Sakila;

use Wandu\Database\Annotations\Cast;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\Table;

/**
 * @Table(name="actor", primaryKey="actor_id", increments=true)
 */
class SakilaActor
{
    /**
     * @Column("actor_id")
     * @Cast("integer")
     * @var int
     */
    private $id;

    /**
     * @Column("first_name")
     * @var string
     */
    private $firstName;

    /**
     * @Column("last_name")
     * @var string
     */
    private $lastName;

    /**
     * @Column("last_update")
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

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
}
