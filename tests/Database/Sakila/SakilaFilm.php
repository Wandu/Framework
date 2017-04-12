<?php
namespace Wandu\Database\Sakila;

use Wandu\Database\Annotations\BelongTo;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\Table;

/**
 * @Table(name="film", primaryKey="film_id", increments=true)
 */
class SakilaFilm
{
    /**
     * @Column(name="film_id", cast="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(name="title")
     * @var string
     */
    private $title;

    /**
     * @Column(name="description")
     * @var string
     */
    private $description;

    /**
     * @Column(name="release_year")
     * @var string
     */
    private $releaseYear;

    /**
     * @Column(name="language_id")
     * @BelongTo(related=SakilaLanguage::class, key="language_id")
     * @var \Wandu\Database\Sakila\SakilaLanguage
     */
    private $language;

    /**
     * SakilaFilm constructor.
     * @param int $id
     * @param string $title
     * @param string $description
     * @param string $releaseYear
     * @param \Wandu\Database\Sakila\SakilaLanguage $language
     */
    public function __construct(
        $id,
        $title,
        $description,
        $releaseYear,
        SakilaLanguage $language
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->releaseYear = $releaseYear;
        $this->language = $language;
    }
}
