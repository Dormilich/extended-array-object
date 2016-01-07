<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class CountValuesTest extends PHPUnit_Framework_TestCase
{
    private $classname = '\Dormilich\Core\ArrayObject';

    public function testCountValuesReturnsArrayObject()
    {
        $xao = new XArray;
        $obj = $xao->countValues();

        $this->assertInstanceOf($this->classname, $obj);
        $this->assertNotSame($xao, $obj);
    }

    public function testCountValues()
    {
        $expected = [7 => 3, 4 => 1, 6 => 3, 5 => 1, 1 => 1, 3 => 1, 2 => 1, 0 => 1];
        $xao = new XArray([7,4,7,6,6,7,5,1,6,3,2,0]);
        $obj = $xao->countValues();

        $this->assertEquals($expected, (array) $obj);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCountValuesWithNonScalarValues()
    {
        $xao = new XArray([1, ['foo']]);
        $obj = $xao->countValues();
    }
}