<?php

namespace TinyRest\Tests\Examples\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity()
 */
class User
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
     * @var string
     *
     * @ORM\Column(name="username", type="string")
     * @Groups({"user:read"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string")
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="hobby", type="string")
     */
    private $hobby;

    /**
     * @ORM\Column(name="birth_date", type="date")
     */
    private $birthDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @var string|null
     */
    private $password;

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
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username) : User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName() : string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName) : User
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName() : string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName) : User
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getHobby() : string
    {
        return $this->hobby;
    }

    /**
     * @param string $hobby
     *
     * @return User
     */
    public function setHobby(string $hobby) : User
    {
        $this->hobby = $hobby;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return User
     */
    public function setEmail(?string $email) : User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate() : \DateTime
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     *
     * @return User
     */
    public function setBirthDate(\DateTime $birthDate) : User
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getTimestamp() : ?\DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime|null $timestamp
     *
     * @return User
     */
    public function setTimestamp(?\DateTime $timestamp) : User
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword() : ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     *
     * @return User
     */
    public function setPassword(?string $password) : User
    {
        $this->password = $password;

        return $this;
    }
}
