<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class ShiftTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	### shift()
	###########################################################################

	public function testShiftOnNumericArray()
	{
		$xao = new XArray([3, 7]);
		
		$this->assertCount(2, $xao);
		$this->assertSame(3,  $xao->shift());
		$this->assertCount(1, $xao);
		$this->assertEquals([7], (array) $xao);
	}

	public function testShiftOnAssocArray()
	{
		$xao = new XArray(['x' => 'bar', 'y' => 'foo']);
		
		$this->assertCount(2, $xao);
		$this->assertSame('bar', $xao->shift());
		$this->assertCount(1, $xao);
		$this->assertEquals(['y' => 'foo'], (array) $xao);
	}

	public function testShiftOnEmptyArray()
	{
		$xao = new XArray([]);
		
		$this->assertCount(0, $xao);
		$this->assertNull($xao->shift());
	}

	### unshift()
	###########################################################################

	public function testUnshiftReturnsArrayObject()
	{
		$xao = new XArray;
		$obj = $xao->unshift('x');

		$this->assertSame($xao, $obj);
	}

	public function testUnshiftAddSingleElement()
	{
		$expected = ['bar', 'foo'];
		$xao = new XArray(['foo']);
		$xao->unshift('bar');

		$this->assertEquals($expected, (array) $xao);
	}

	public function testUnshiftAddMultipleElements()
	{
		$expected = ['bar', 1, false, 'foo'];
		$xao = new XArray(['foo']);
		$xao->unshift('bar', 1, false);

		$this->assertEquals($expected, (array) $xao);
	}

	public function testUnshiftRetainAssocKeys()
	{
		$expected = ['bar', 'foo' => 5];
		$xao = new XArray(['foo' => 5]);
		$xao->unshift('bar');

		$this->assertEquals($expected, (array) $xao);
	}
}
