<?php
namespace Wandu\Database\Sakila;

use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\Table;

/**
 * @Table(name="film", primaryKey="film_id", increments=true)
 */
class SakilaFilm
{
    /**
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
