<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class SpliceTest extends PHPUnit_Framework_TestCase
{
    private $classname = '\Dormilich\Core\ArrayObject';

    public function testSpliceReturnsArrayObject()
    {
        $xao = new XArray([1,2,3]);
        $obj = $xao->splice(0);

        $this->assertInstanceOf($this->classname, $obj);
        $this->assertNotSame($xao, $obj);
    }

    public function testSpliceModifiesObject()
    {
        $xao = new XArray([1,2,3]);
        $obj = $xao->splice(2);

        $this->assertEquals([1,2], (array) $xao);
    }

    public function testSpliceWithOffsetOnly()
    {
        $xao = new XArray([1,2,3,4,5]);
        
        // offset 0
        $test = clone $xao;
        $this->assertEquals([1,2,3,4,5], (array) $test->splice(0));
        $this->assertEquals([], (array) $test);
        // offset 0 < x < length
        $test = clone $xao;
        $this->assertEquals([3,4,5], (array) $test->splice(2));
        $this->assertEquals([1,2], (array) $test);
        $test = clone $xao;
        $this->assertEquals([4,5], (array) $test->splice(-2));
        $this->assertEquals([1,2,3], (array) $test);
        // offset max
        $test = clone $xao;
        $this->assertEquals([], (array) $test->splice(5));
        $this->assertEquals([1,2,3,4,5], (array) $test);
        $test = clone $xao;
        $this->assertEquals([1,2,3,4,5], (array) $test->splice(-5));
        $this->assertEquals([], (array) $test);
        // offset > length
        $test = clone $xao;
        $this->assertEquals([], (array) $test->splice(10));
        $this->assertEquals([1,2,3,4,5], (array) $test);
        $test = clone $xao;
        $this->assertEquals([1,2,3,4,5], (array) $test->splice(-10));
        $this->assertEquals([], (array) $test);
    }

    public function testSpliceWithLength()
    {
        $xao = new XArray([1,2,3,4,5]);

        // 'normal' case
        $test = clone $xao;
        $this->assertEquals([2,3], (array) $test->splice(1, 2));
        $this->assertEquals([1,4,5], (array) $test);
        $test = clone $xao;
        $this->assertEquals([3,4], (array) $test->splice(-3, 2));
        $this->assertEquals([1,2,5], (array) $test);
        // length > size
        $test = clone $xao;
        $this->assertEquals([4,5], (array) $test->splice(3, 5));
        $this->assertEquals([1,2,3], (array) $test);
        $test = clone $xao;
        $this->assertEquals([3,4,5], (array) $test->splice(-3, 5));
        $this->assertEquals([1,2], (array) $test);
    }

    public function testSpliceWithTwoPositions()
    {
        $xao = new XArray([1,2,3,4,5]);

        // 'normal' case
        $test = clone $xao;
        $this->assertEquals([2,3], (array) $test->splice(1, -2));
        $this->assertEquals([1,4,5], (array) $test);
        $test = clone $xao;
        $this->assertEquals([3], (array) $test->splice(-3, -2));
        $this->assertEquals([1,2,4,5], (array) $test);
        // end < start
        $test = clone $xao;
        $this->assertEquals([], (array) $test->splice(3, -4));
        $this->assertEquals([1,2,3,4,5], (array) $test);
        $test = clone $xao;
        $this->assertEquals([], (array) $test->splice(-3, -4));
        $this->assertEquals([1,2,3,4,5], (array) $test);
    }

    public function testSpliceWithReplacements()
    {
        $xao = new XArray([1,2,3,4,5]);

        // replace
        $test = clone $xao;
        $test->splice(1, 2, ['a','b','c']);
        $this->assertEquals([1,'a','b','c',4,5], (array) $test);
        // insert
        $test = clone $xao;
        $test->splice(1, 0, ['a','b']);
        $this->assertEquals([1,'a','b',2,3,4,5], (array) $test);
        // with type cast
        $test = clone $xao;
        $test->splice(1, 3, 'a');
        $this->assertEquals([1,'a',5], (array) $test);
    }
}