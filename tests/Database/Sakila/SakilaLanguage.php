<?php
namespace Wandu\Database\Sakila;

use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\Table;

/**
 * @Table(name="language", primaryKey="language_id", increments=true)
 */
class SakilaLanguage
{
    /**
     * @Column(name="language_id", cast="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(name="name")
     * @var string
     */
    private $name;

    /**
     * @Column(name="last_update", cast="datetime")
     * @var string
     */
    private $lastUpdate;

    /**
     * SakilaLanguage constructor.
     * @param int $id
     * @param string $name
     * @param string $lastUpdate
     */
    public function __construct($id, $name, $lastUpdate)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastUpdate = $lastUpdate;
    }
}
