<?php
namespace Wandu\Database\Sakila\Models;

use Carbon\Carbon;
use Wandu\Database\Annotations\Cast;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\Table;

/**
 * @Table(name="language", primaryKey="language_id", increments=true)
 */
class SakilaLanguage
{
    /**
     * @Column("language_id")
     * @Cast("integer")
     * @var int
     */
    private $id;

    /**
     * @Column(name="name")
     * @var string
     */
    private $name;

    /**
     * @Column("last_update")
     * @Cast("datetime")
     * @var \Carbon\Carbon
     */
    private $lastUpdate;

    public function __construct($name, Carbon $lastUpdate)
    {
        $this->name = $name;
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return int
     */
    public function getId()
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
     * @return \Carbon\Carbon
     */
    public function getLastUpdate(): Carbon
    {
        return $this->lastUpdate;
    }
}
