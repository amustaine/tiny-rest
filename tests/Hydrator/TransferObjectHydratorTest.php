<?php

namespace TinyRest\Tests\Hydrator;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use TinyRest\Annotations\Property;
use TinyRest\Hydrator\TransferObjectHydrator;
use TinyRest\Tests\Examples\DTO\UserTransferObject;
use TinyRest\TransferObject\TransferObjectInterface;

class TransferObjectHydratorTest extends TestCase
{
    public function testEmptyJsonObject()
    {
        $request = Request::create('localhost', 'POST', [], [], [], [], json_encode([]));
        $transferObject         = new UserTransferObject();
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->assertNotEmpty($transferObject);
    }

    public function testInvalidJsonException()
    {
        $request = Request::create('localhost', 'POST', [], [], [], [], 'userName=John');
        $transferObject         = new UserTransferObject();
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);

        $this->expectExceptionMessage('Invalid JSON');
        $transferObjectHydrator->hydrate($request);
    }

    public function testWithGet()
    {
        $request = Request::create('localhost', 'GET', ['name' => 'John Doe']);

        $transferObject         = new UserTransferObject();
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->assertEquals('John Doe', $transferObject->name);
    }

    public function testWithNonGet()
    {
        $data = ['name' => 'John Doe'];
        $request = Request::create('localhost', 'POST', [], [], [], [], json_encode($data));

        $transferObject         = new UserTransferObject();
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->assertEquals('John Doe', $transferObject->name);
    }

    public function testWithCustomParamName()
    {
        $request                = Request::create('localhost', 'GET', ['user_name' => 'John']);
        $transferObject         = new UserTransferObject();
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->assertEquals('John', $transferObject->userName);
    }

    public function testWithUnmapped()
    {
        $request                = Request::create('localhost', 'GET', [
            'lifeStyle' => 'Actor',
            'name'      => 'John Doe',
            'email'     => 'foo@bar.baz'
        ]);
        $transferObject         = new UserTransferObject();
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->assertEquals('Actor', $transferObject->lifeStyle);
        $this->assertEquals('John Doe', $transferObject->name);
        $this->assertNull($transferObject->email);
    }

    public function testWithMethodCallback()
    {
        $data = [
            'name' => 'John Doe',
        ];
        $request = Request::create('localhost', 'POST', [], [], [], [], json_encode($data));

        $transferObject         = new UserTransferObject();
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->assertEquals('John', $transferObject->firstName);
        $this->assertEquals('Doe', $transferObject->lastName);
    }

    public function testWithStaticCallback()
    {
        $data = [
            'name' => 'John Doe',
        ];
        $request = Request::create('localhost', 'POST', [], [], [], [], json_encode($data));

        $transferObject         = new UserTransferObject();
        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->assertEquals(date('Ymd'), $transferObject->date);
    }

    public function testPropertyAccessorWhenNoParam()
    {
        $data = [
            'fieldA' => 'John Doe',
        ];
        $request = Request::create('localhost', 'POST', [], [], [], [], json_encode($data));

        $transferObject = new class implements TransferObjectInterface
        {
            /**
             * @Property()
             */
            private $fieldA;

            /**
             * @Property()
             */
            private $fieldB;

            public function getFieldA() : string
            {
                return $this->fieldA;
            }

            public function setFieldA(string $value)
            {
                $this->fieldA = $value;
            }

            public function getFieldB()
            {
                return $this->fieldB;
            }

            public function setFieldB()
            {
                $this->fieldB = 'TEST';
            }
        };

        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->assertEquals('John Doe', $transferObject->getFieldA());
        $this->assertNull($transferObject->getFieldB());
    }
}
