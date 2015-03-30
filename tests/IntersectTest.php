<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

/*
	mapping table for array functions to methods:

	+===================+=====+=======+===========+============+==============+
	| PHP               | key | value | intersect | kintersect |  aintersect  |
	+===================+=====+=======+===========+============+==============+
	| intersect         |  -  |   x   | ()        |     --     |      --      |
	+-------------------+-----+-------+-----------+------------+--------------+
	| uintersect        |  -  |   u   | (fn)      |     --     |      --      |
	+-------------------+-----+-------+-----------+------------+--------------+
	| intersect_key     |  x  |   -   |     -     | ()         |      --      |
	+-------------------+-----+-------+-----------+------------+--------------+
	| intersect_ukey    |  u  |   -   |     -     | (fn)       |      --      |
	+-------------------+-----+-------+-----------+------------+--------------+
	| intersect_assoc   |  x  |   x   |     -     |     --     | ()           |
	+-------------------+-----+-------+-----------+------------+--------------+
	| intersect_uassoc  |  u  |   x   |     -     |     --     | (null, fn)   |
	|                   |     |       |           |            | (fn, KEY)    |
	+-------------------+-----+-------+-----------+------------+--------------+
	| uintersect_assoc  |  x  |   u   |     -     |     --     | (fn, null)   |
	|                   |     |       |           |            | (fn, VALUE)  |
	+-------------------+-----+-------+-----------+------------+--------------+
	| uintersect_uassoc |  u  |   u   |     -     |     --     | (fn, fn)     |
	+-------------------+-----+-------+-----------+------------+--------------+

	NOTE: the result of any array_intersect() function cannot have more members 
	than the length of the shortest comparison array even if the comparison 
	callback would allow for more

//*/

class IntersectTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	### intersect()
	###########################################################################

	public function testIntersectReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->intersect([2, 3]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testIntersectWithArray()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->intersect([2, 3, 'a' => 'y']);

		$this->assertEquals([1 => 2, 'x' => 'y'], (array) $obj);
	}

	public function testIntersectWithMultipleArrays()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->intersect([1, 2, 3, 4, 5, 'x', 'y'], [2, 4, 'bar']);

		$this->assertEquals([1 => 2, 2 => 4], (array) $obj);
	}

	public function testIntersectWithArrayObject()
	{
		$xao1 = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$xao2 = new XArray([1, 2, 3, 4, 5, 'x', 'y']);
		$obj  = $xao1->intersect($xao2);

		$this->assertEquals([1, 2, 'x' => 'y', 2 => 4], (array) $obj);
	}

	public function testIntersectWithNonArray()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);

		$obj = $xao->intersect('bar', 1, 2, 3);
		$this->assertEquals([], (array) $obj);

		$obj = $xao->intersect('bar', [1, 'bar']);
		$this->assertEquals(['foo' => 'bar'], (array) $obj);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testIntersectWithoutArrayFails()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->intersect('length_compare_func');
	}

	public function testIntersectAcceptsFunction()
	{
		$xao = new XArray([1, 22, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->intersect(['xxx', 18], 'length_compare_func');

		$this->assertEquals([1 => 22, 'foo' => 'bar'], (array) $obj);
	}

	public function testIntersectAcceptsClosure()
	{
		$xao = new XArray([1, 22, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->intersect(['xxx', 18], function ($a, $b) {
			return length_compare_func($a, $b);
		});

		$this->assertEquals([1 => 22, 'foo' => 'bar'], (array) $obj);
	}

	public function testIntersectAcceptsStaticCallback()
	{
		$xao = new XArray([1, 22, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->intersect(['xxx', 18], ['CallbackTestMethods', 'static_length_compare']);

		$this->assertEquals([1 => 22, 'foo' => 'bar'], (array) $obj);
	}

	public function testIntersectAcceptsCallback()
	{
		$xao = new XArray([1, 22, 'foo' => 'bar', 'x' => 'y', 4]);
		$test = new CallbackTestMethods;
		$obj  = $xao->intersect(['xxx', 18], [$test, 'length_compare']);

		$this->assertEquals([1 => 22, 'foo' => 'bar'], (array) $obj);
	}

	### kintersect()
	###########################################################################

	public function testKIntersectReturnsArrayObject()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->kintersect(['x' => 4, 'z' => 5]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testKIntersectWithArray()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->kintersect(['x' => 4, 'z' => 5]);

		$this->assertEquals(['x' => 1, 'z' => 3], (array) $obj);
	}

	public function testKIntersectWithMultipleArrays()
	{
		$xao = new XArray(['x' => 1, 4, 'y' => 2, 'z' => 3, 5]);
		$obj = $xao->kintersect(['z' => 4, 3], ['z' => 5]);

		$this->assertEquals(['z' => 3], (array) $obj);
	}

	public function testKIntersectWithNonArray()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);

		$obj = $xao->kintersect('a', 'z');
		$this->assertEquals(['z' => 3], (array) $obj);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testKIntersectWithInvalidNonArray()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->kintersect(new \stdClass);
	}

	public function testKIntersectWithArrayObject()
	{
		$xao1 = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$xao2 = new XArray(['x' => 4, 'z' => 5]);
		$obj  = $xao1->kintersect($xao2);

		$this->assertEquals(['x' => 1, 'z' => 3], (array) $obj);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testKIntersectWithoutArrayFails()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->kintersect('length_compare_func');
	}

	public function testKIntersectAcceptsFunction()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$obj = $xao->kintersect(['a' => 4], 'length_compare_func');

		$this->assertEquals(['x' => 1], (array) $obj);
	}

	public function testKIntersectAcceptsClosure()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$obj = $xao->kintersect(['a' => 4], function ($a, $b) {
			return length_compare_func($a, $b);
		});

		$this->assertEquals(['x' => 1], (array) $obj);
	}

	public function testKIntersectAcceptsStaticCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$obj = $xao->kintersect(['a' => 4], ['CallbackTestMethods', 'static_length_compare']);

		$this->assertEquals(['x' => 1], (array) $obj);
	}

	public function testKIntersectAcceptsCallback()
	{
		$xao  = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$test = new CallbackTestMethods;
		$obj  = $xao->kintersect(['a' => 4], [$test, 'length_compare']);

		$this->assertEquals(['x' => 1], (array) $obj);
	}

	### aintersect()
	###########################################################################

	public function testAIntersectReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		// it does not _need_ a callback to work
		$obj = $xao->aintersect([2, 3]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testAIntersectWithArray()
	{
		$xao = new XArray(['x', 'ab', 'z']);
		$obj = $xao->aintersect(['x', 'z']);

		$this->assertEquals(['x'], (array) $obj);
	}

	public function testAIntersectWithArrayObject()
	{
		$xao1 = new XArray(['x', 'ab', 'z']);
		$xao2 = new XArray(['x', 'z']);
		$obj  = $xao1->aintersect($xao2);

		$this->assertEquals(['x'], (array) $obj);
	}

	public function testAIntersectWithMultipleArrays()
	{
		$xao = new XArray(['x', 'ab', 'z']);
		$obj = $xao->aintersect(['x', 'z'], ['x', 'y', 'z']);

		$this->assertEquals(['x'], (array) $obj);
	}

	public function testAIntersectWithValueCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);

		$obj = $xao->aintersect(['ab' => 22, 'z' => 'a'], 'length_compare_func', null);
		$this->assertEquals(['z' => 3], (array) $obj);

		$obj = $xao->aintersect(['ab' => 22, 'z' => 'a'], 'length_compare_func', XAInterface::COMPARE_VALUE);
		$this->assertEquals(['z' => 3], (array) $obj);
	}

	public function testAIntersectWithKeyCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 22, 'z' => 3]);

		$obj = $xao->aintersect(['ab' => 2, 'z' => 1], null, 'length_compare_func');
		$this->assertEquals(['x' => 1], (array) $obj);

		$obj = $xao->aintersect(['ab' => 2, 'z' => 1], 'length_compare_func', XAInterface::COMPARE_KEY);
		$this->assertEquals(['x' => 1], (array) $obj);
	}

	public function testAIntersectWithKeyValueCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);

		$obj = $xao->aintersect(['xy' => 5, 'z' => 1], 'length_compare_func', 'length_compare_func');
		$this->assertEquals(['x' => 1, 'ab' => 2], (array) $obj);
	}

	### xintersect()
	###########################################################################

	public function testXIntersectReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->xintersect([2, 3]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testXIntersectWithArray()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->xintersect([2, 3, 'a' => 'y']);

		$this->assertEquals([0 => 2, 'a' => 'y'], (array) $obj);
	}

	public function testXIntersectWithArrayObject()
	{
		$xao1 = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$xao2 = new XArray([1, 2, 3, 4, 5, 'x', 'y']);
		$obj  = $xao1->xintersect($xao2);

		$this->assertEquals([1, 2, 6 => 'y', 3 => 4], (array) $obj);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testXIntersectWithoutArrayFails()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->xintersect('length_compare_func');
	}

	public function testXIntersectAcceptsFunction()
	{
		$xao = new XArray(['foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->xintersect(['xxx', 18], 'length_compare_func');

		$this->assertEquals(['xxx'], (array) $obj);
	}

	public function testXIntersectAcceptsClosure()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->xintersect(['xxx', 18], function ($a, $b) {
			return length_compare_func($a, $b);
		});

		$this->assertEquals(['xxx'], (array) $obj);
	}

	public function testXIntersectAcceptsStaticCallback()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$obj = $xao->xintersect(['xxx', 18], ['CallbackTestMethods', 'static_length_compare']);

		$this->assertEquals(['xxx'], (array) $obj);
	}

	public function testXIntersectAcceptsCallback()
	{
		$xao = new XArray([1, 2, 'foo' => 'bar', 'x' => 'y', 4]);
		$test = new CallbackTestMethods;
		$obj  = $xao->xintersect(['xxx', 18], [$test, 'length_compare']);

		$this->assertEquals(['xxx'], (array) $obj);
	}

	### xkintersect()
	###########################################################################

	public function testXKIntersectReturnsArrayObject()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->xkintersect(['x' => 4, 'z' => 5]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testXKIntersectWithArray()
	{
		$xao = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj = $xao->xkintersect(['a' => 4, 'z' => 5]);

		$this->assertEquals(['z' => 5], (array) $obj);
	}

	public function testXKIntersectWithArrayObject()
	{
		$xao1 = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$xao2 = new XArray(['a' => 4, 'z' => 5]);
		$obj  = $xao1->xkintersect($xao2);

		$this->assertEquals(['z' => 5], (array) $obj);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testXKIntersectWithoutArrayFails()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->xkintersect('length_compare_func');
	}

	public function testXKIntersectAcceptsFunction()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$obj = $xao->xkintersect(['a' => 4, 'abc' => 'xyz'], 'length_compare_func');

		$this->assertEquals(['a' => 4], (array) $obj);
	}

	public function testXKIntersectAcceptsClosure()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$obj = $xao->xkintersect(['a' => 4, 'abc' => 'xyz'], function ($a, $b) {
			return length_compare_func($a, $b);
		});

		$this->assertEquals(['a' => 4], (array) $obj);
	}

	public function testXKIntersectAcceptsStaticCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$obj = $xao->xkintersect(['a' => 4, 'abc' => 'xyz'], ['CallbackTestMethods', 'static_length_compare']);

		$this->assertEquals(['a' => 4], (array) $obj);
	}

	public function testXKIntersectAcceptsCallback()
	{
		$xao  = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);
		$test = new CallbackTestMethods;
		$obj  = $xao->xkintersect(['a' => 4, 'abc' => 'xyz'], [$test, 'length_compare']);

		$this->assertEquals(['a' => 4], (array) $obj);
	}

	### xaintersect()
	###########################################################################

	public function testXAIintersectReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		// it does not _need_ a callback to work
		$obj = $xao->xaintersect([2, 3]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testXAIintersectWithArray()
	{
		$xao = new XArray(['x', 'ab', 'z']);
		$obj = $xao->xaintersect(['x', 'z']);

		$this->assertEquals(['x'], (array) $obj);
	}

	public function testXAIintersectWithArrayObject()
	{
		$xao1 = new XArray(['x', 'ab', 'z']);
		$xao2 = new XArray(['x', 'z']);
		$obj  = $xao1->xaintersect($xao2);

		$this->assertEquals(['x'], (array) $obj);
	}

	public function testXAIintersectWithValueCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);

		$obj = $xao->xaintersect(['ab' => 22, 'z' => 'a'], 'length_compare_func', null);
		$this->assertEquals(['z' => 'a'], (array) $obj);

		$obj = $xao->xaintersect(['ab' => 22, 'z' => 'a'], 'length_compare_func', XAInterface::COMPARE_VALUE);
		$this->assertEquals(['z' => 'a'], (array) $obj);
	}

	public function testXAIintersectWithKeyCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 22, 'z' => 3]);

		$obj = $xao->xaintersect(['ab' => 2, 'z' => 1], null, 'length_compare_func');
		$this->assertEquals(['z' => 1], (array) $obj);

		$obj = $xao->xaintersect(['ab' => 2, 'z' => 1], 'length_compare_func', XAInterface::COMPARE_KEY);
		$this->assertEquals(['z' => 1], (array) $obj);
	}

	public function testXAIintersectWithKeyValueCallback()
	{
		$xao = new XArray(['x' => 1, 'ab' => 2, 'z' => 3]);

		$obj = $xao->xaintersect(['xy' => 5, 'z' => 11], 'length_compare_func', 'length_compare_func');
		$this->assertEquals(['xy' => 5], (array) $obj);
	}
}