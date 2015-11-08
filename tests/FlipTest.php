<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class FlipTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	public function testFlipReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->flip();
		
		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testFlipNumericValues()
	{
		$array    = [1 => 'foo', 3 => 'bar'];
		$expected = ['foo' => 1, 'bar' => 3];
		$xao      = new XArray($array);
		
		$this->assertEquals($expected, (array) $xao->flip());
	}

	public function testFlipStringValues()
	{
		$array    = ['foo' => 1, 'bar' => 3];
		$expected = [1 => 'foo', 3 => 'bar'];
		$xao      = new XArray($array);
		
		$this->assertEquals($expected, $xao->flip()->getArrayCopy());
	}

	public function testFlipWithMultipleValueArray()
	{
		$array    = ['a', 'b', 'c', 'b'];
		$expected = ['a' => 0, 'b' => 3, 'c' => 2, ];
		$xao      = new XArray($array);
		
		$this->assertEquals($expected, $xao->flip()->getArrayCopy());
	}

	/**
	 * @expectedException ErrorException
	 */
	public function testFlipWithInvalidArray()
	{
		$array = [true, null, ['foo' => 'bar']];
		$xao   = new XArray($array);
		$obj   = $xao->flip();
	}
}