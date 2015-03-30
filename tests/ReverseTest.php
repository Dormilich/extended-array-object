<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class ReverseTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	public function testReverseReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->reverse();

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testReverseSuccess()
	{
		$expected = [3, 2, 1];
		$xao      = new XArray([1 => 1, 8 => 2, 4 => 3]);
		$obj      = $xao->reverse();

		$this->assertEquals($expected, (array) $obj);
	}

	public function testReverseRetainsAssocKeys()
	{
		$expected = ['c' => 3, 'b' => 2, 'a' => 1];
		$xao      = new XArray(['a' => 1, 'b' => 2, 'c' => 3]);
		$obj      = $xao->reverse();

		$this->assertEquals($expected, (array) $obj);
	}

	public function testReversePreserveNumericKeys()
	{
		$expected = [2 => 3, 1 => 2, 0 => 1];
		$xao      = new XArray([1, 2, 3]);
		$obj      = $xao->reverse(true);

		$this->assertEquals($expected, (array) $obj);
	}
}
