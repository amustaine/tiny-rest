<?php

namespace TinyRest\Tests\Hydrator;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use TinyRest\Annotations\Mapping;
use TinyRest\Annotations\Property;
use TinyRest\Annotations\Relation;
use TinyRest\Hydrator\MetaReader;
use TinyRest\TransferObject\TransferObjectInterface;

class MetaReaderTest extends TestCase
{
    public function testRelations()
    {
        $class = new class implements TransferObjectInterface
        {
            /**
             * @Property()
             * @Relation(byField="foo")
             */
            public $foo;
        };

        $metaReader = new MetaReader($class);

        $relations = $metaReader->getRelations();

        $this->assertNotEmpty($relations);
        $this->assertCount(1, $relations);
        $this->assertTrue(isset($relations['foo']));
        $this->assertEquals('foo', $relations['foo']->byField);
    }

    public function testMapping()
    {
        $class = new class implements TransferObjectInterface
        {
            /**
             * @Property()
             */
            public $foo;

            /**
             * @Mapping()
             */
            public $bar;

            public $baz;
        };

        $metaReader = new MetaReader($class);

        $mapping = $metaReader->getMapping();

        $this->assertNotEmpty($mapping);
        $this->assertCount(1, $mapping);
    }
}
