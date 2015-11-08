<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class UniqueTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	public function testUniqueReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->unique();

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testUniqueSuccess()
	{
		$expected = [0 => 8, 1 => 7, 2 => 4, 3 => 3, 4 => 2, 6 => 6];
		$xao = new XArray([8,7,4,3,2,3,6,7,8,3,4,6]);
		$obj = $xao->unique();

		$this->assertEquals($expected, (array) $obj);
	}

	/**
	 * @expectedException ErrorException
	 */
	public function testUniqueFailsOnConversionProblems()
	{
		$xao = new XArray(['foo', ['bar'], 'bar']);
		$obj = $xao->unique();
	}

	// I couldn’t find an example, where it made a difference
	# public function testUniqueSortVariants(){}
}
