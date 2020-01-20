<?php

namespace TinyRest\Tests\Examples\Entity;

use Doctrine\ORM\Mapping as ORM;
use TinyRest\Annotations\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"testItem:read", "user:read"}},
 *     denormalizationContext={"groups"={"testItem:write"}}
 * )
 * @ORM\Entity()
 */
class TestItem
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
     * @ORM\Column(type="string", nullable=false)
     * @Groups({"testItem:read", "testItem:write"})
     */
    private $name;

    /**
     * @ORM\Column(name="additional_name", type="string", nullable=false)
     * @Groups({"testItem:read", "testItem:write"})
     */
    private $additionalName;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @Groups({"user:read", "user:write"})
     */
    protected $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAdditionalName(): ?string
    {
        return $this->additionalName;
    }

    public function setAdditionalName(string $additionalName)
    {
        $this->additionalName = $additionalName;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
