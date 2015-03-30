<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

/*
	mapping table for array functions to methods:

	+==============+=====+=======+============+=============+=============+
	| PHP          | key | value |    diff    |    kdiff    |    adiff    |
	+==============+=====+=======+============+=============+=============+
	| diff         |  -  |   x   | ()         |     ---     |     ---     |
	+--------------+-----+-------+------------+-------------+-------------+
	| udiff        |  -  |   u   | (fn)       |     ---     |     ---     |
	+--------------+-----+-------+------------+-------------+-------------+
	| diff_key     |  x  |   -   |     --     | ()          |     ---     |
	+--------------+-----+-------+------------+-------------+-------------+
	| diff_ukey    |  u  |   -   |     --     | (fn)        |     ---     |
	+--------------+-----+-------+------------+-------------+-------------+
	| diff_assoc   |  x  |   x   |     --     |     ---     | ()          |
	+--------------+-----+-------+------------+-------------+-------------+
	| diff_uassoc  |  u  |   x   |     --     |     ---     | (null, fn)  |
	|              |     |       |            |             | (fn, KEY)   |
	+--------------+-----+-------+------------+-------------+-------------+
	| udiff_assoc  |  x  |   u   |     --     |     ---     | (fn, null)  |
	|              |     |       |            |             | (fn, VALUE) |
	+--------------+-----+-------+------------+-------------+-------------+
	| udiff_uassoc |  u  |   u   |     --     |     ---     | (fn, fn)    |
	+--------------+-----+-------+------------+-------------+-------------+
//*/

class DiffTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	### diff()
	###########################################################################

	public function testDiffReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->diff([2, 3]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testDiffWithArray()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->diff([2, 3]);

		$this->assertEquals([0 => 1, 'foo' => 'bar', 'x' => 'y', 2 => 4], (array) $obj);
	}

	public function testDiffWithMultipleArrays()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->diff([1, 3, 5], ['x', 'y']);

		$this->assertEquals([1 => 2, 'foo' => 'bar', 2 => 4], (array) $obj);
	}

	public function testDiffWithArrayObject()
	{
		$xao1 = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$xao2 = new XArray([2, 3]);
		$obj  = $xao1->diff($xao2);

		$this->assertEquals([0 => 1, 'foo' => 'bar', 'x' => 'y', 2 => 4], (array) $obj);
	}

	public function testDiffWithNonArray()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->diff(2, 'bar');

		$this->assertEquals([0 => 1, 'x' => 'y', 2 => 4], (array) $obj);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testDiffWithoutArrayFails()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->diff('length_compare_func');
	}

	public function testDiffAcceptsFunction()
	{
		$source = [
			0 => 'foo', 
			1 => 'bar', 
			2 => 'ab', 
			3 => 'x', 
			4 => 'f-g-h', 
		];
		$expected = [
			2 => 'ab', 
			3 => 'x', 
			4 => 'f-g-h', 
		];
		$xao = new XArray($source);
		$obj = $xao->diff(['abc'], 'length_compare_func');

		$this->assertEquals($expected, (array) $obj);
	}

	public function testDiffAcceptsClosure()
	{
		$source = [
			0 => 'foo', 
			1 => 'bar', 
			2 => 'ab', 
			3 => 'x', 
			4 => 'f-g-h', 
		];
		$expected = [
			2 => 'ab', 
			3 => 'x', 
			4 => 'f-g-h', 
		];
		$xao = new XArray($source);
		$obj = $xao->diff(['abc'], function ($a, $b) {
			return length_compare_func($a, $b);
		});

		$this->assertEquals($expected, (array) $obj);
	}

	public function testDiffAcceptsStaticCallback()
	{
		$source = [
			0 => 'foo', 
			1 => 'bar', 
			2 => 'ab', 
			3 => 'x', 
			4 => 'f-g-h', 
		];
		$expected = [
			2 => 'ab', 
			3 => 'x', 
			4 => 'f-g-h', 
		];
		$xao = new XArray($source);
		$obj = $xao->diff(['abc'], ['CallbackTestMethods', 'static_length_compare']);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testDiffAcceptsCallback()
	{
		$source = [
			0 => 'foo', 
			1 => 'bar', 
			2 => 'ab', 
			3 => 'x', 
			4 => 'f-g-h', 
		];
		$expected = [
			2 => 'ab', 
			3 => 'x', 
			4 => 'f-g-h', 
		];
		$xao = new XArray($source);
		$test = new CallbackTestMethods;
		$obj  = $xao->diff(['abc'], [$test, 'length_compare']);

		$this->assertEquals($expected, (array) $obj);
	}

	### kdiff()
	###########################################################################

	public function testKDiffReturnsArrayObject()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->kdiff(['x' => 4, 'z' => 5]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testKDiffWithArray()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->kdiff(['x' => 4, 'z' => 5]);

		$this->assertEquals(['y' => 2], (array) $obj);
	}

	public function testKDiffWithMultipleArrays()
	{
		$xao = new XArray(['x' => 1, 4, 'y' => 2, 'z' => 3, 5]);
		$obj = $xao->kdiff(['x' => 4, 3], ['z' => 5]);

		$this->assertEquals(['y' => 2, 1 => 5], (array) $obj);
	}

	public function testKDiffWithNonArray()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->kdiff('a', 'z');

		$this->assertEquals(['x' => 1, 'y' => 2], (array) $obj);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testKDiffWithInvalidNonArray()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->kdiff(new \stdClass);
	}

	public function testKDiffWithArrayObject()
	{
		$xao1 = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$xao2 = new XArray(['x' => 4, 'z' => 5]);
		$obj  = $xao1->kdiff($xao2);

		$this->assertEquals(['y' => 2], (array) $obj);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testKDiffWithoutArrayFails()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->kdiff('length_compare_func');
	}

	public function testKDiffAcceptsFunction()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$obj = $xao->kdiff(['x' => 4], 'length_compare_func');

		$this->assertEquals(['ab' => 2], (array) $obj);
	}

	public function testKDiffAcceptsClosure()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$obj = $xao->kdiff(['x' => 4], function ($a, $b) {
			return length_compare_func($a, $b);
		});

		$this->assertEquals(['ab' => 2], (array) $obj);
	}

	public function testKDiffAcceptsStaticCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$obj = $xao->kdiff(['x' => 4], ['CallbackTestMethods', 'static_length_compare']);

		$this->assertEquals(['ab' => 2], (array) $obj);
	}

	public function testKDiffAcceptsCallback()
	{
		$xao  = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$test = new CallbackTestMethods;
		$obj  = $xao->kdiff(['x' => 4], [$test, 'length_compare']);

		$this->assertEquals(['ab' => 2], (array) $obj);
	}

	### adiff()
	###########################################################################

	public function testADiffReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		// it does not _need_ a callback to work
		$obj = $xao->adiff([2, 3]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testADiffWithArray()
	{
		$xao = new XArray(['x', 'ab', 'z']);
		$obj = $xao->adiff(['x', 'z']);

		$this->assertEquals([1 => 'ab', 2 => 'z'], (array) $obj);
	}

	public function testADiffWithArrayObject()
	{
		$xao1 = new XArray(['x', 'ab', 'z']);
		$xao2 = new XArray(['x', 'z']);
		$obj  = $xao1->adiff($xao2);

		$this->assertEquals([1 => 'ab', 2 => 'z'], (array) $obj);
	}

	public function testADiffWithMultipleArrays()
	{
		$xao = new XArray(['x', 'ab', 'z']);
		$obj = $xao->adiff(['x', 'z'], ['x', 'y', 'z']);

		$this->assertEquals([1 => 'ab'], (array) $obj);
	}

	public function testADiffWithValueCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);

		$obj = $xao->adiff(['ab' => 22, 'z' => 'a'], 'length_compare_func', null);
		$this->assertEquals(['x' => 1, 'ab' => 2], (array) $obj);

		$obj = $xao->adiff(['ab' => 22, 'z' => 'a'], 'length_compare_func', XAInterface::COMPARE_VALUE);
		$this->assertEquals(['x' => 1, 'ab' => 2], (array) $obj);
	}

	public function testADiffWithKeyCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);

		$obj = $xao->adiff(['ab' => 2, 'z' => 1], null, 'length_compare_func');
		$this->assertEquals(['z' => 3], (array) $obj);

		$obj = $xao->adiff(['ab' => 2, 'z' => 1], 'length_compare_func', XAInterface::COMPARE_KEY);
		$this->assertEquals(['z' => 3], (array) $obj);
	}

	public function testADiffWithKeyValueCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 22, 'z' => 3]);

		$obj = $xao->adiff(['ab' => 2, 'z' => 1], 'length_compare_func', 'length_compare_func');
		$this->assertEquals(['ab' => 22], (array) $obj);
	}

	// test some more invalid parameter combinations

	### xdiff()
	###########################################################################

	public function testXDiffReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->xdiff([2, 3]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testXDiffWithArray()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->xdiff([2, 3, 'y']);

		$this->assertEquals([1 => 3], (array) $obj);
	}

	public function testXDiffWithArrayObject()
	{
		$xao1 = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$xao2 = new XArray([2, 3, 'y']);
		$obj  = $xao1->xdiff($xao2);

		$this->assertEquals([1 => 3], (array) $obj);
	}

	public function testXDiffWithFunction()
	{
		$xao = new XArray([1, 2, 333]);
		$obj = $xao->xdiff(['a', 'bb'], 'length_compare_func');

		$this->assertEquals([1 => 'bb'], (array) $obj);
	}

	### xkdiff()
	###########################################################################

	public function testXKDiffReturnsArrayObject()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->xkdiff(['x' => 4, 'z' => 5]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testXKDiffWithArray()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->xkdiff(['x' => 4, 'a' => 5]);

		$this->assertEquals(['a' => 5], (array) $obj);
	}
/*
	// not sure if that’s a good idea to support ...
	public function testXKDiffWithNonArray()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->xkdiff('a', 'z');

		$this->assertEquals(['a' => 0], (array) $obj);
	}
	/**
	 * @expectedException RuntimeException
	 *
	public function testXKDiffWithInvalidNonArray()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->xkdiff(new \stdClass);
	}
*/

	public function testXKDiffWithArrayObject()
	{
		$xao1 = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$xao2 = new XArray(['a' => 4, 'z' => 5]);
		$obj  = $xao1->xkdiff($xao2);

		$this->assertEquals(['a' => 4], (array) $obj);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testXKDiffWithoutArrayFails()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->xkdiff('length_compare_func');
	}

	public function testXKDiffAcceptsFunction()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->xkdiff(['ab' => 4, 'foo', 'bar'], 'length_compare_func');

		$this->assertEquals(['ab' => 4], (array) $obj);
	}

	### xadiff()
	###########################################################################

	public function testXADiffReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->xadiff([2, 3]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testXADiffWithArray()
	{
		$xao = new XArray(['x', 'ab', 'z']);
		$obj = $xao->xadiff(['x', 'y']);

		$this->assertEquals([1 => 'y'], (array) $obj);
	}

	public function testXADiffWithArrayObject()
	{
		$xao1 = new XArray(['x', 'ab', 'z']);
		$xao2 = new XArray(['x', 'y']);
		$obj  = $xao1->xadiff($xao2);

		$this->assertEquals([1 => 'y'], (array) $obj);
	}

	public function testXADiffWithValueCallback()
	{
		$xao = new XArray(['ab' => 22, 'z' => 'a']);

		$obj = $xao->xadiff(['x' => 1, 'ab' => 2, 'z' => 3], 'length_compare_func', null);
		$this->assertEquals(['x' => 1, 'ab' => 2], (array) $obj);

		$obj = $xao->xadiff(['x' => 1, 'ab' => 2, 'z' => 3], 'length_compare_func', XAInterface::COMPARE_VALUE);
		$this->assertEquals(['x' => 1, 'ab' => 2], (array) $obj);
	}

	public function testXADiffWithKeyCallback()
	{
		$xao = new XArray(['ab' => 2, 'z' => 1]);

		$obj = $xao->xadiff(['x' => 1, 'ab' => 2, 'z' => 3], null, 'length_compare_func');
		$this->assertEquals(['z' => 3], (array) $obj);

		$obj = $xao->xadiff(['x' => 1, 'ab' => 2, 'z' => 3], 'length_compare_func', XAInterface::COMPARE_KEY);
		$this->assertEquals(['z' => 3], (array) $obj);
	}

	public function testXADiffWithKeyValueCallback()
	{
		$xao = new XArray(['ab' => 2, 'z' => 1]);

		$obj = $xao->xadiff(['x' => 1, 'ab' => 22, 'z' => 3], 'length_compare_func', 'length_compare_func');
		$this->assertEquals(['ab' => 22], (array) $obj);
	}
}