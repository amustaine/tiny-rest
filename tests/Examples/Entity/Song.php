<?php

namespace TinyRest\Tests\Examples\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="songs")
 * @ORM\Entity()
 */
class Song
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
     * @ORM\Column(name="name", type="string")
     * @var string
     */
    private $name;

    /**
     * @var Album
     *
     * @ORM\ManyToOne(targetEntity="Album")
     */
    private $album;

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
     * @return Song
     */
    public function setName(string $name) : Song
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Album
     */
    public function getAlbum() : Album
    {
        return $this->album;
    }

    /**
     * @param Album $album
     *
     * @return Song
     */
    public function setAlbum(Album $album) : Song
    {
        $this->album = $album;

        return $this;
    }
}
