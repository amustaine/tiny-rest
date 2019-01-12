<?php

namespace TinyRest\Tests;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
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
             * @Relation(class="SomeClass", byField="foo")
             */
            public $foo;
        };

        $metaReader = new MetaReader($class);

        $relations = $metaReader->getRelations();

        $this->assertNotEmpty($relations);
        $this->assertCount(1, $relations);
        $this->assertTrue(isset($relations['foo']));
        $this->assertEquals('SomeClass', $relations['foo']->class);
        $this->assertEquals('foo', $relations['foo']->byField);
    }
}
