<?php

namespace TinyRest\Tests\Converter;

use TinyRest\Converter\EntityConverter;
use TinyRest\Tests\DatabaseTestCase;
use TinyRest\Tests\Examples\Entity\Album;

class EntityConverterTest extends DatabaseTestCase
{
    public function testPlainStructureConvert()
    {
        $objectMeta = (new EntityConverter($this->getEntityManager()))->createObjectMetaFromEntity(Album::class, []);

        $this->assertTrue(in_array('tmpField', $objectMeta->getProperties()));
        $this->assertTrue(in_array('name', $objectMeta->getProperties()));
        $this->assertTrue(in_array('year', $objectMeta->getProperties()));

        $this->assertArrayHasKey('artist', $objectMeta->getRelations());
        $this->assertArrayHasKey('cover', $objectMeta->getRelations());
    }
}
