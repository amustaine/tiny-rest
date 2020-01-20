<?php

namespace TinyRest\Tests\Annotations;

use PHPUnit\Framework\TestCase;
use TinyRest\Annotations\ResourceReader;
use TinyRest\Tests\Examples\Entity\TestItem;

class ResourceReaderTest extends TestCase
{
    public function testRead()
    {
        $rr = new ResourceReader();
        $rr->read(TestItem::class);
    }
}
