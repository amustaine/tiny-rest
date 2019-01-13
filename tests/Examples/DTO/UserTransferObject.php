<?php

namespace TinyRest\Tests\Examples\DTO;

use TinyRest\Annotations\Mapping;
use TinyRest\Annotations\OnObjectHydrated;
use TinyRest\Annotations\Property;
use TinyRest\TransferObject\TransferObjectInterface;

/**
 * @OnObjectHydrated(method="parseName")
 * @OnObjectHydrated(callback={"TinyRest\Tests\Examples\DTO\UserTransferObject", "setDate"})
 */
class UserTransferObject implements TransferObjectInterface
{
    /**
     * @Property()
     * @Mapping(mapped=false)
     */
    public $name;

    /**
     * @Property(name="user_name")
     * @Mapping(column="username")
     */
    public $userName;

    /**
     * @Property(mapped=false)
     * @Mapping(mapped=false)
     */
    public $email;

    /**
     * @Property(mapped=false)
     */
    public $firstName;

    /**
     * @Property(mapped=false)
     */
    public $lastName;

    /**
     * @Property()
     * @Mapping(column="hobby")
     */
    public $lifeStyle;

    public $date;

    /**
     * @Property()
     */
    public $birthDate;

    public function parseName()
    {
        $parts           = explode(' ', $this->name);
        $this->firstName = $parts[0] ?? null;
        $this->lastName  = $parts[1] ?? null;
    }

    public static function setDate(UserTransferObject $object)
    {
        $object->date = date('Ymd');
    }
}
