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

            public function setFieldB($value)
            {
                $this->fieldB = 'TEST';
            }
        };

        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->assertEquals('John Doe', $transferObject->getFieldA());
        $this->assertNull($transferObject->getFieldB());
    }

    public function testTypeCast()
    {
        $request = Request::create('localhost', 'GET', [
                'integer'  => '28',
                'float'    => '16.3',
                'string'   => '11',
                'array'    => 'a,b,c,d, e',
                'datetime' => '2016-05-15'
            ]
        );

        $transferObject = new class implements TransferObjectInterface
        {
            /**
             * @Property(type="integer")
             */
            public $integer;

            /**
             * @Property(type="float")
             */
            public $float;

            /**
             * @Property(type="string")
             */
            public $string;

            /**
             * @Property(type="array")
             */
            public $array;

            /**
             * @Property(type="datetime")
             */
            public $datetime;
        };

        $transferObjectHydrator = new TransferObjectHydrator($transferObject);
        $transferObjectHydrator->hydrate($request);

        $this->assertTrue(28 === $transferObject->integer);
        $this->assertTrue(16.3 === $transferObject->float);
        $this->assertTrue('11' === $transferObject->string);

        $this->assertTrue(is_array($transferObject->array));
        $this->assertCount(5, $transferObject->array);
        $this->assertEquals('c', $transferObject->array[2]);

        $this->assertTrue($transferObject->datetime instanceof \DateTime);
        $this->assertEquals(new \DateTime('2016-05-15'), $transferObject->datetime);
    }

    public function testUnknownTypeCast()
    {
        $request = Request::create('localhost', 'GET', ['field' => 'abc']);

        $transferObject = new class implements TransferObjectInterface
        {
            /**
             * @Property(type="superint")
             */
            public $field;
        };

        $transferObjectHydrator = new TransferObjectHydrator($transferObject);

        $this->expectException(\InvalidArgumentException::class);
        $transferObjectHydrator->hydrate($request);
    }
}
