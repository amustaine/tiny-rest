<?php

namespace TinyRest\Tests\Examples\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Album
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $year;

    /**
     * @var Artist
     *
     * @ORM\ManyToOne(targetEntity="Artist")
     */
    private $artist;

    /**
     * @var Cover
     *
     * @ORM\ManyToOne(targetEntity="Cover")
     */
    private $cover;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    private $tmpField = 'Hello!';

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
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
     * @return Album
     */
    public function setName(string $name) : Album
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getYear() : int
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return Album
     */
    public function setYear(int $year) : Album
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return Artist
     */
    public function getArtist() : Artist
    {
        return $this->artist;
    }

    /**
     * @param Artist $artist
     *
     * @return Album
     */
    public function setArtist(Artist $artist) : Album
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * @return Cover
     */
    public function getCover() : Cover
    {
        return $this->cover;
    }

    /**
     * @param Cover $cover
     *
     * @return Album
     */
    public function setCover(Cover $cover) : Album
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return string
     */
    public function getTmpField() : string
    {
        return $this->tmpField;
    }

    /**
     * @param string $tmpField
     *
     * @return Album
     */
    public function setTmpField(string $tmpField) : Album
    {
        $this->tmpField = $tmpField;

        return $this;
    }
}
