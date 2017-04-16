<?php
namespace Wandu\Database\Sakila;

use Wandu\Database\Annotations\Cast;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\RelatedToOne;
use Wandu\Database\Annotations\Table;

/**
 * @Table(name="city", primaryKey="city_id", increments=true)
 */
class SakilaCity
{
    /**
     * @Column("city_id")
     * @Cast("integer")
     * @var int
     */
    private $id;

    /**
     * @Column(name="city")
     * @var string
     */
    private $name;

    /**
     * @Column("last_update")
     * @Cast("datetime")
     * @var string
     */
    private $lastUpdate;

    /**
     * @Column(name="country_id")
     * @RelatedToOne(related=\Wandu\Database\Sakila\SakilaCountry::class, key="country_id")
     * @var \Wandu\Database\Sakila\SakilaCountry
     */
    private $country;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLastUpdate(): string
    {
        return $this->lastUpdate;
    }

    /**
     * @return \Wandu\Database\Sakila\SakilaCountry
     */
    public function getCountry()
    {
        return $this->country;
    }
}
