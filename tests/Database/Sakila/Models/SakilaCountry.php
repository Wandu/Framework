<?php
namespace Wandu\Database\Sakila\Models;

use Wandu\Database\Annotations\Cast;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\HasMany;
use Wandu\Database\Annotations\Table;

/**
 * @Table(name="country", primaryKey="country_id", increments=true)
 */
class SakilaCountry
{
    /**
     * @Column("country_id")
     * @Cast("integer")
     * @var int
     */
    private $id;

    /**
     * @Column(name="country")
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
     * @HasMany(related=\Wandu\Database\Sakila\Models\SakilaCity::class, key="country_id")
     * @var \Wandu\Collection\Contracts\ListInterface|\Wandu\Database\Sakila\Models\SakilaCity[]
     */
    private $cities;

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
     * @return \Wandu\Collection\Contracts\ListInterface|\Wandu\Database\Sakila\Models\SakilaCity[]
     */
    public function getCities()
    {
        return $this->cities;
    }
}
