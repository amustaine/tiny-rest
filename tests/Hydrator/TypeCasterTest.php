<?php

namespace TinyRest\Tests\Hydrator;

use PHPUnit\Framework\TestCase;
use TinyRest\Hydrator\TypeCaster;

class TypeCasterTest extends TestCase
{
    public function testInteger()
    {
        $value = (new TypeCaster())->getInteger('25');

        $this->assertTrue(is_int($value));
        $this->assertEquals(25, $value);
    }

    public function testIntegerWithInvalidString()
    {
        $value = (new TypeCaster())->getInteger('af25');

        $this->assertEquals(0, $value);
    }

    public function testFloat()
    {
        $value = (new TypeCaster())->getFloat('25');

        $this->assertTrue(is_float($value));
        $this->assertEquals(25, $value);
    }

    public function testFloatWithInvalidString()
    {
        $value = (new TypeCaster())->getFloat('af25');

        $this->assertEquals(0, $value);
    }

    public function testBoolean()
    {
        $value = (new TypeCaster())->getBoolean('1');

        $this->assertTrue(is_bool($value));
        $this->assertEquals(true, $value);
    }

    public function testFalseBoolean()
    {
        $value = (new TypeCaster())->getBoolean('0');

        $this->assertTrue(is_bool($value));
        $this->assertEquals(false, $value);
    }

    public function testStringTrueBoolean()
    {
        $value = (new TypeCaster())->getBoolean('true');

        $this->assertTrue(is_bool($value));
        $this->assertEquals(true, $value);
    }

    public function testStringFalseBoolean()
    {
        $value = (new TypeCaster())->getBoolean('false');

        $this->assertTrue(is_bool($value));
        $this->assertEquals(false, $value);
    }

    public function testDateTime()
    {
        $format = '2018-10-10 18:25';

        $value = (new TypeCaster())->getDateTime($format);

        $this->assertTrue($value instanceof \DateTime);
        $this->assertEquals(new \DateTime($format), $value);
    }

    public function testW3CDateTime()
    {
        $format = '2018-10-10T18:25:05+01';

        $value = (new TypeCaster())->getDateTime($format);

        $this->assertTrue($value instanceof \DateTime);
        $this->assertEquals(new \DateTime($format), $value);
    }

    public function testInvalidDateTime()
    {
        $format = 'Invalid format';

        $value = (new TypeCaster())->getDateTime($format);

        $this->assertNull($value);
    }

    public function testArray()
    {
        $value = 'A, B, C, D, E';

        $value = (new TypeCaster())->getArray($value);

        $this->assertTrue(is_array($value));
        $this->assertCount(5, $value);
        $this->assertEquals('B', $value[1]);
    }

    public function testArrayWithMultipleSpaces()
    {
        $value = 'A,  B%dAf;     ,   C,          D,         E';

        $value = (new TypeCaster())->getArray($value);

        $this->assertTrue(is_array($value));
        $this->assertCount(5, $value);
        $this->assertEquals('B%dAf;', $value[1]);
    }

    public function testWithOneElement()
    {
        $value = 'A';

        $value = (new TypeCaster())->getArray($value);

        $this->assertTrue(is_array($value));
        $this->assertCount(1, $value);
        $this->assertEquals('A', $value[0]);
    }

    public function testString()
    {
        $value = 15;

        $value = (new TypeCaster())->getString($value);

        $this->assertTrue(is_string($value));
        $this->assertEquals('15', $value);
    }
}
