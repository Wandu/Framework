<?php
namespace Wandu\Database\Sakila;

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
     * @var string
     */
    private $lastUpdate;

    public function __construct($id, $name, $lastUpdate)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastUpdate = $lastUpdate;
    }
}
