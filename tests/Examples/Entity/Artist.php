<?php

namespace TinyRest\Tests\Examples\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Artist
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id = 2;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $genre;

    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Artist
     */
    public function setName(string $name) : Artist
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getYear() :? int
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return Artist
     */
    public function setYear(int $year) : Artist
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return string
     */
    public function getGenre() : ?string
    {
        return $this->genre;
    }

    /**
     * @param string $genre
     *
     * @return Artist
     */
    public function setGenre(string $genre) : Artist
    {
        $this->genre = $genre;

        return $this;
    }
}
