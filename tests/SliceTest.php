<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class SliceTest extends PHPUnit_Framework_TestCase
{
    private $classname = '\Dormilich\Core\ArrayObject';

    public function testSliceReturnsArrayObject()
    {
        $xao = new XArray([1,2,3]);
        $obj = $xao->slice(0);

        $this->assertInstanceOf($this->classname, $obj);
        $this->assertNotSame($xao, $obj);
    }

    public function testSliceWithOffsetOnly()
    {
        $xao = new XArray([1,2,3,4,5]);
        
        // offset 0
        $this->assertEquals([1,2,3,4,5], (array) $xao->slice(0));
        // offset 0 < x < length
        $this->assertEquals([3,4,5], (array) $xao->slice(2));
        $this->assertEquals([4,5], (array) $xao->slice(-2));
        // offset max
        $this->assertEquals([], (array) $xao->slice(5));
        $this->assertEquals([1,2,3,4,5], (array) $xao->slice(-5));
        // offset > length
        $this->assertEquals([], (array) $xao->slice(10));
        $this->assertEquals([1,2,3,4,5], (array) $xao->slice(-10));
    }

    public function testSliceWithLength()
    {
        $xao = new XArray([1,2,3,4,5]);

        // 'normal' case
        $this->assertEquals([2,3], (array) $xao->slice(1, 2));
        $this->assertEquals([3,4], (array) $xao->slice(-3, 2));
        // length > size
        $this->assertEquals([4,5], (array) $xao->slice(3, 5));
        $this->assertEquals([3,4,5], (array) $xao->slice(-3, 5));
    }

    public function testSliceWithTwoPositions()
    {
        $xao = new XArray([1,2,3,4,5]);

        // 'normal' case
        $this->assertEquals([2,3], (array) $xao->slice(1, -2));
        $this->assertEquals([3], (array) $xao->slice(-3, -2));
        // end < start
        $this->assertEquals([], (array) $xao->slice(3, -4));
        $this->assertEquals([], (array) $xao->slice(-3, -4));
    }

    public function testSlicePreservingKeys()
    {
        $xao = new XArray(['x' => 8, 'y' => 3, 'z' => 7]);

        // without
        $this->assertEquals([3,7], (array) $xao->slice(1, 2));
        $this->assertEquals([3,7], (array) $xao->slice(1, 2, XAInterface::IGNORE_KEYS));
        // with
        $this->assertEquals(['y' => 3, 'z' => 7], (array) $xao->slice(1, 2, XAInterface::PRESERVE_KEYS));
        // without length
        $this->assertEquals(['z' => 7], (array) $xao->slice(1, NULL, XAInterface::PRESERVE_KEYS));
    }
}