<?php
namespace Wandu\Database\Sakila;

use Wandu\Database\Annotations\Cast;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\RelatedToOne;
use Wandu\Database\Annotations\Table;

/**
 * @Table(name="film", primaryKey="film_id", increments=true)
 */
class SakilaFilm
{
    /**
     * @Column(name="film_id")
     * @Cast("integer")
     * @var int
     */
    private $id;

    /**
     * @Column(name="title")
     * @var string
     */
    private $title;

    /**
     * @Column("description")
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
     * @RelatedToOne(related=SakilaLanguage::class, key="language_id")
     * @var \Wandu\Database\Sakila\SakilaLanguage
     */
    private $language;

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

    /**
     * @return \Wandu\Database\Sakila\SakilaLanguage
     */
    public function getLanguage(): SakilaLanguage
    {
        return $this->language;
    }
}
