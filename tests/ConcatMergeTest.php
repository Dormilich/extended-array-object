<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class ConcatMergeTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	### concat()
	###########################################################################

	public function testConcatReturnsArrayObject()
	{
		$xao = new XArray;
		$obj = $xao->concat([]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testConcatNumericArrayAddSingleArray()
	{
		$expected = [1, 2, 3, 'x', 'y', 'z'];
		$xao      = new XArray([1, 2, 3]);
		$obj      = $xao->concat(['x', 'y', 'z']);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testConcatNumericArrayAddMultipleArrays()
	{
		$expected = [1, 2, 3, 'z', 'x', 'y'];
		$xao      = new XArray([1, 2, 3]);
		$obj      = $xao->concat(['z'], ['x', 'y']);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testConcatNumericArrayAddArrayObject()
	{
		$expected = [1, 2, 3, 'a', 'z'];
		$xao1     = new XArray([1, 2, 3]);
		$xao2     = new XArray(['a', 'z']);
		$obj      = $xao1->concat($xao2);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testConcatAssocArrayAddSingleArray()
	{
		$expected = ['x' => 1, 'y' => 2, 'z' => 2, 'a' => 1];
		$xao      = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj      = $xao->concat(['a' => 1, 'z' => 2]);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testConcatAssocArrayAddMultipleArrays()
	{
		$expected = ['x' => 5, 'y' => 2, 'z' => 4, 'a' => 1];
		$xao      = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj      = $xao->concat(['a' => 1, 'z' => 2], ['x' => 5, 'z' => 4]);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testConcatAssocArrayAddArrayObject()
	{
		$expected = ['x' => 1, 'y' => 2, 'z' => 2, 'a' => 1];
		$xao1     = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$xao2     = new XArray(['a' => 1, 'z' => 2]);
		$obj      = $xao1->concat($xao2);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testConcatAddScalarValue()
	{
		$expected = [1, 2, 3, 'a', 'z'];
		$xao      = new XArray([1, 2, 3]);
		$obj      = $xao->concat('a', 'z');

		$this->assertEquals($expected, (array) $obj);
	}

	### merge()
	###########################################################################

	public function testMergeReturnsArrayObject()
	{
		$xao = new XArray;
		$obj = $xao->merge([]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testMergeNumericArrayWithSingleArray()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->merge(['x', 'y']);

		$this->assertEquals(['x', 'y', 3], (array) $obj);
	}

	public function testMergeNumericArrayWithMultipleArrays()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->merge(['x', 'y'], ['z']);

		$this->assertEquals(['z', 'y', 3], (array) $obj);
	}

	public function testMergeNumericArrayWithArrayObject()
	{
		$xao1 = new XArray([1, 2, 3]);
		$xao2 = new XArray(['a', 'z']);
		$obj  = $xao1->merge($xao2);

		$this->assertEquals(['a', 'z', 3], (array) $obj);
	}

	public function testMergeAssocArrayWithSingleArray()
	{
		$expected = ['x' => 1, 'y' => 2, 'z' => 2, 'a' => 1];
		$xao      = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj      = $xao->merge(['a' => 1, 'z' => 2]);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testMergeAssocArrayWithMultipleArrays()
	{
		$expected = ['x' => 5, 'y' => 2, 'z' => 4, 'a' => 1];
		$xao      = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj      = $xao->merge(['a' => 1, 'z' => 2], ['x' => 5, 'z' => 4]);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testMergeAssocArrayWithArrayObject()
	{
		$expected = ['x' => 1, 'y' => 2, 'z' => 2, 'a' => 1];
		$xao1     = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$xao2     = new XArray(['a' => 1, 'z' => 2]);
		$obj      = $xao1->merge($xao2);

		$this->assertEquals($expected, (array) $obj);
	}
}