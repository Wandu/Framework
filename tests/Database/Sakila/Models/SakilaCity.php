<?php
namespace Wandu\Database\Sakila\Models;

use Wandu\Database\Annotations\Cast;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\BelongsTo;
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
     * @BelongsTo(related=\Wandu\Database\Sakila\Models\SakilaCountry::class, key="country_id")
     * @var \Wandu\Database\Sakila\Models\SakilaCountry
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
     * @return \Wandu\Database\Sakila\Models\SakilaCountry
     */
    public function getCountry()
    {
        return $this->country;
    }
}
