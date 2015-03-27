<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

function test_filter() 
{ 
	return true; 
}

function test_map($value) 
{ 
	return $value; 
}

function test_reduce($carry, $value)
{
	return $carry;
}

function length_compare_func($a, $b)
{
	$la = strlen((string) $a);
	$lb = strlen((string) $b);

	if ($la === $lb) {
		return 0;
	} elseif ($la > $lb) {
		return 1;
	} else {
		return -1;
	}
}

class CallbackTestMethods
{
	public function filter()
	{
		return true;
	}

	public static function static_filter()
	{
		return true;
	}

	public function map($value)
	{
		return $value;
	}

	public static function static_map($value)
	{
		return $value;
	}

	public function reduce($carry, $value)
	{
		return $carry;
	}

	public static function static_reduce($carry, $value)
	{
		return $carry;
	}

	public function length_compare($a, $b)
	{
		return length_compare_func($a, $b);
	}

	public static function static_length_compare($a, $b)
	{
		return length_compare_func($a, $b);
	}
}

class ArrayObjectTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	public function testArrayAccessInterfaceExists()
	{
		$xao = new XArray;

		$this->assertInstanceOf('ArrayAccess', $xao);
	}

	/**
     * @depends testArrayAccessInterfaceExists
     */
	public function testArrayAccessNumericArray()
	{
		$xao = new XArray([1, 2, 3]);

		// offsetExists
		$this->assertTrue(isset($xao[0])); 
		// offsetGet
		$this->assertSame(2, $xao[1]); 
		// offsetSet
		$xao[2] = 'x';
		$this->assertSame('x', $xao[2]);
		// offsetUnset
		unset($xao[0]);
		$this->assertArrayNotHasKey(0, $xao);
	}

	/**
     * @depends testArrayAccessInterfaceExists
     */
	public function testArrayAccessAssocArray()
	{
		$xao = new XArray([
			'foo' => 1, 
			'bar' => 2, 
			'y'   => 3, 
		]);

		// offsetExists
		$this->assertTrue(isset($xao['foo'])); 
		// offsetGet
		$this->assertSame(2, $xao['bar']); 
		// offsetSet
		$xao['y'] = 'x';
		$this->assertSame('x', $xao['y']);
		// offsetUnset
		unset($xao['foo']);
		$this->assertArrayNotHasKey('foo', $xao);
	}

	public function testIteratorAggregateInterfaceExists()
	{
		$xao = new XArray;

		$this->assertInstanceOf('IteratorAggregate', $xao);
	}

	/**
     * @depends testIteratorAggregateInterfaceExists
     */
	public function testIteratorAggregate()
	{
		$xao = new XArray;

		$this->assertInstanceOf('ArrayIterator', $xao->getIterator());
	}

	public function testSerializableInterfaceExists()
	{
		$xao = new XArray;

		$this->assertInstanceOf('Serializable', $xao);
	}

	/**
     * @depends testSerializableInterfaceExists
     */
	public function testSerializable()
	{
		$xao = new XArray([1, 2, 3]);

		$serialized = serialize($xao);
		$object     = unserialize($serialized);

		$this->assertEquals($xao, $object);
		$this->assertNotSame($xao, $object);

		$this->assertInternalType('string', $serialized);
	}

	public function testCountableInterfaceExists()
	{
		$xao = new XArray;

		$this->assertInstanceOf('Countable', $xao);
	}

	/**
     * @depends testCountableInterfaceExists
     */
	public function testCountable()
	{
		$xao = new XArray([1, 2, 3]);

		$this->assertSame(3, count($xao));
		$this->assertSame(3, sizeof($xao));
	}

	public function testArrayObjectParentExists()
	{
		$xao = new XArray;

		$this->assertInstanceOf('ArrayObject', $xao);
	}

	/**
     * @depends testArrayObjectParentExists
     */
	public function testArrayObjectGetArrayCopy()
	{
		$array = [
			'foo' => 1, 
			'bar' => 2, 
		];
		$xao = new XArray($array);

		$this->assertEquals($array, $xao->getArrayCopy());
	}

	/**
     * @depends testArrayObjectGetArrayCopy
     */
	public function testArrayObjectExchangeArray()
	{
		$array = [
			'foo' => 1, 
			'bar' => 2, 
		];
		$xao = new XArray($array);

		$replace = [1, 2, 3];
		$xao->exchangeArray($replace);

		$this->assertEquals($replace, $xao->getArrayCopy());
	}

	/**
     * @depends testArrayObjectParentExists
     */
	public function testArrayObjectCastToArray()
	{
		$array = [
			'foo' => 1, 
			'bar' => 2, 
		];
		$xao = new XArray($array);

		$this->assertEquals($array, (array) $xao);
	}

	/**
     * @depends testArrayObjectGetArrayCopy
     */
	public function testConstructorHasCorrectDefaultValue()
	{
		$xao      = new XArray;
		$expected = [];

		$this->assertEquals($expected, $xao->getArrayCopy());
	}

	public function testJsonSerializableInterfaceExists()
	{
		$xao = new XArray;

		$this->assertInstanceOf('JsonSerializable', $xao);
	}

	/**
     * @depends testJsonSerializableInterfaceExists
     */
	public function testJsonSerializable()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);
		$json  = json_encode($xao);

		$this->assertEquals($array, json_decode($json, true));
	}

	### changeKeyCase()
	#######################################################

	public function testChangeKeyCaseToUpper()
	{
		$xao = new XArray([
			'fOo' => 1, 
			'Bar' => 2, 
		]);
		$expected = [
			'FOO' => 1, 
			'BAR' => 2, 
		];
		$obj = $xao->changeKeyCase(\CASE_UPPER);
		$this->assertEquals($expected, (array) $obj);

		$this->assertNotEquals($xao, $obj);
		$this->assertInstanceOf($this->classname, $obj);
	}

	public function testChangeKeyCaseToLower()
	{
		$xao = new XArray([
			'fOo' => 1, 
			'Bar' => 2, 
		]);
		$expected = [
			'foo' => 1, 
			'bar' => 2, 
		];
		$obj = $xao->changeKeyCase(\CASE_LOWER);
		$this->assertEquals($expected, (array) $obj);

		$this->assertNotEquals($xao, $obj);
		$this->assertInstanceOf($this->classname, $obj);
	}

	public function testChangeKeyCaseUsingDefault()
	{
		$xao = new XArray([
			'fOo' => 1, 
			'Bar' => 2, 
		]);
		$expected = [
			'foo' => 1, 
			'bar' => 2, 
		];
		$obj = $xao->changeKeyCase();
		$this->assertEquals($expected, (array) $obj);

		$this->assertNotEquals($xao, $obj);
		$this->assertInstanceOf($this->classname, $obj);
	}

	public function invalidCaseConstantProvider()
	{
		return [['foo'], [29], [8.7], [null], ['']];
	}

	/**
     * @dataProvider invalidCaseConstantProvider
     */
	public function testChangeKeyCaseUsingBogusParameter($param)
	{
		// array_change_key_case() uses CASE_UPPER for integers that do not 
		// match one of the constants
		$xao = new XArray([
			'fOo' => 1, 
			'Bar' => 2, 
		]);
		$expected = [
			'foo' => 1, 
			'bar' => 2, 
		];
		$obj = $xao->changeKeyCase($param);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertEquals($expected, (array) $obj);
		$this->assertNotEquals($xao, $obj);
	}

	### contains()
	#######################################################

	public function testContainsEqualWithExistingValue()
	{
		$xao = new XArray([1, 2, 3]);

		$this->assertSame(true, $xao->contains(2, false));
		$this->assertSame(true, $xao->contains('2', false));
	}

	public function testContainsDefaultWithExistingValue()
	{
		$xao = new XArray([1, 2, 3]);

		$this->assertSame(true, $xao->contains(2));
		$this->assertSame(true, $xao->contains('2'));
	}

	public function testContainsIdenticalWithExistingValue()
	{
		$xao = new XArray([1, 2, 3]);

		$this->assertSame(true,  $xao->contains(2, true));
		$this->assertSame(false, $xao->contains('2', true));
	}

	public function testContainsWithNonExistingValue()
	{
		$xao = new XArray([1, 2, 3]);

		$this->assertSame(false, $xao->contains('foo', true));
		$this->assertSame(false, $xao->contains('foo', false));
		$this->assertSame(false, $xao->contains('foo'));
	}

	public function testContainsOnEmptyArray()
	{
		$xao = new XArray([]);
		
		$this->assertSame(false, $xao->contains('foo', true));
		$this->assertSame(false, $xao->contains('foo', false));
		$this->assertSame(false, $xao->contains('foo'));
	}

	### diff()
	#######################################################
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
	#######################################################

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
	#######################################################

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

	### filter()
	#######################################################

	public function testFilterAcceptsFunction()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);

		$xao->filter('test_filter');

		$this->assertEquals($array, (array) $xao);
	}

	public function testFilterAcceptsClosure()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);

		$xao->filter(function () {
			return true;
		});

		$this->assertEquals($array, (array) $xao);
	}

	public function testFilterAcceptsStaticCallback()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);

		$obj   = $xao->map(['CallbackTestMethods', 'static_filter']);

		$this->assertEquals($array, (array) $obj);
	}

	public function testFilterAcceptsCallback()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);
		$test  = new CallbackTestMethods;

		$obj   = $xao->map([$test, 'filter']);

		$this->assertEquals($array, (array) $obj);
	}

	/**
     * @depends testFilterAcceptsClosure
     */
	public function testFilterReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);

		$obj = $xao->filter(function ($value) { return true; });

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	/**
     * @depends testFilterAcceptsClosure
     */
	public function testFilterValueCallbackParameter()
	{
		$xao      = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$expected = ['y' => 2, 'z' => 3];

		$obj      = $xao->filter(function ($value) { 
			return ($value > 1); 
		});

		$this->assertEquals($expected, (array) $obj);
	}

	/**
     * @depends testFilterAcceptsClosure
     */
	public function testFilterKeyValueCallbackParameters()
	{
		if (!defined('ARRAY_FILTER_USE_BOTH')) {
			return;
		}

		$xao      = new XArray([2 => 3, 5 => 1, 7 => 9]);
		$expected = [2 => 3, 7 => 9];

		$obj      = $xao->filter(function ($value, $key) { 
			return ($value > $key); 
		});

		$this->assertEquals($expected, (array) $obj);
	}

	/**
     * @depends testFilterAcceptsClosure
     */
	public function testFilterBindsArrayObjectToClosure()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);

		$obj   = $xao->filter(function () {
			return ($this instanceof XArray);
		});
		$this->assertEquals($array, (array) $obj);
	}

	/**
	 * @expectedException LogicException
	 */
	public function testFilterFailsForInvalidCallback()
	{
		$xao = new XArray([1, 2]);

		// ArrayObject::count() is non-static and accepts no parameters
		// though the syntax for the callable is correct the execution will fail
		$xao->filter(['ArrayObject', 'count']);
	}

	### flip()
	#######################################################

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
	 * @expectedException RuntimeException
	 */
	public function testFlipWithInvalidArray()
	{
		$array = [true, null, ['foo' => 'bar']];
		$xao   = new XArray($array);
		$obj   = $xao->flip();
	}

	### join()
	#######################################################

	public function testJoinReturnsString()
	{
		$xao = new XArray;

		$this->assertInternalType('string', $xao->join());
	}

	public function testJoinWithScalarArrayWithoutGlue()
	{
		$xao = new XArray([13, 2.87, null, 'foo', true, false]);

		$this->assertSame('132.87foo1', $xao->join());
	}

	public function testJoinWithScalarArrayWithGlue()
	{
		$xao = new XArray([13, 2.87, null, 'foo', true, false]);

		$this->assertSame('13, 2.87, , foo, 1, ', $xao->join(', '));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testJoinWithNonscalarArray()
	{
		$xao = new XArray([1, ['foo'], 7]);

		$xao->join('-');
	}

	### keys()
	#######################################################

	public function testKeysReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->keys();

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testKeysReturnsKeyValues()
	{
		$expected = [0 => 'foo', 1 => 'x'];
		$xao      = new XArray(['foo' => 'bar', 'x' => 'y']);
		$obj      = $xao->keys();

		$this->assertCount(2, $obj);
		$this->assertEquals($expected, (array) $obj);
	}

	public function testKeysFilteredByEqualValue()
	{
		$expected = [0 => 'x'];
		$xao      = new XArray(['foo' => 'bar', 'x' => 'y']);
		$obj      = $xao->keys('y', false);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testKeysFilteredByEqualValueDefaultFlag()
	{
		$expected = [0 => 'x'];
		$xao      = new XArray(['foo' => 'bar', 'x' => 'y']);
		$obj      = $xao->keys('y');

		$this->assertEquals($expected, (array) $obj);
	}

	public function testKeysFilteredByIdenticalValue()
	{
		$expected = [0 => 'bar'];
		$xao      = new XArray(['foo' => true, 'bar' => 1, 'x' => '1']);
		$obj      = $xao->keys(1, true);

		$this->assertEquals($expected, (array) $obj);
	}

	### map()
	#######################################################

	public function testMapAcceptsFunction()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);
		$obj   = $xao->map('test_map');

		$this->assertEquals($array, (array) $obj);
	}

	public function testMapAcceptsClosure()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);

		$obj   = $xao->map(function ($value) {
			return $value;
		});

		$this->assertEquals($array, (array) $obj);
	}

	public function testMapAcceptsStaticCallback()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);

		$obj   = $xao->map(['CallbackTestMethods', 'static_map']);

		$this->assertEquals($array, (array) $obj);
	}

	public function testMapAcceptsCallback()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);
		$test  = new CallbackTestMethods;

		$obj   = $xao->map([$test, 'map']);

		$this->assertEquals($array, (array) $obj);
	}

	/**
	 * @expectedException LogicException
	 */
	public function testMapFailsForInvalidCallback()
	{
		$xao = new XArray([1, 2]);

		// ArrayObject::count() is non-static and accepts no parameters
		// though the syntax for the callable is correct the execution will fail
		$xao->map(['ArrayObject', 'count']);
	}

	/**
	 * @depends testMapAcceptsFunction
	 */
	public function testMapReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->map('test_map');
		
		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	/**
     * @depends testMapAcceptsClosure
     */
	public function testMapCallbackParameters()
	{
		$xao      = new XArray(['bar' => ' jeder Vernunft']);
		$expected = ['bar jeder Vernunft'];

		$obj      = $xao->map(function ($value, $key) { 
			return $key . $value; 
		});

		$this->assertEquals($expected, (array) $obj);
	}

	/**
     * @depends testMapAcceptsClosure
     */
	public function testMapBindsArrayObjectToClosure()
	{
		$xao   = new XArray([1, 2, 3]);
		$array = [2, 4, 6];

		$obj   = $xao->map(function ($value) {
			if ($this instanceof XArray) {
				return $value * 2;
			}
			return 1;
		});
		$this->assertEquals($array, (array) $obj);
	}

	/**
	 * @depends testMapAcceptsFunction
	 */
	public function testMapDefaultReindexesArray()
	{
		$xao      = new XArray(['x' => 28, 'y' => 68, 'z' => 4]);
		$expected = [28, 68, 4];

		$this->assertEquals($expected, (array) $xao->map('test_map'));
	}

	/**
	 * @depends testMapAcceptsFunction
	 */
	public function testMapReindexesArray()
	{
		$xao      = new XArray(['x' => 28, 'y' => 68, 'z' => 4]);
		$expected = [28, 68, 4];

		$this->assertEquals($expected, (array) $xao->map('test_map', false));
	}

	/**
	 * @depends testMapAcceptsFunction
	 */
	public function testMapPreserveKeys()
	{
		$array = ['x' => 28, 'y' => 68, 'z' => 4];
		$xao   = new XArray($array);

		$this->assertEquals($array, (array) $xao->map('test_map', true));
	}

	### merge()
	#######################################################

	public function testMergeReturnsArrayObject()
	{
		$xao = new XArray;
		$obj = $xao->merge([]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testMergeNumericArrayAddSingleArray()
	{
		$expected = [1, 2, 3, 'x', 'y', 'z'];
		$xao      = new XArray([1, 2, 3]);
		$obj      = $xao->merge(['x', 'y', 'z']);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testMergeNumericArrayAddMultipleArrays()
	{
		$expected = [1, 2, 3, 'z', 'x', 'y'];
		$xao      = new XArray([1, 2, 3]);
		$obj      = $xao->merge(['z'], ['x', 'y']);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testMergeNumericArrayAddArrayObject()
	{
		$expected = [1, 2, 3, 'a', 'z'];
		$xao1     = new XArray([1, 2, 3]);
		$xao2     = new XArray(['a', 'z']);
		$obj      = $xao1->merge($xao2);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testMergeAssocArrayAddSingleArray()
	{
		$expected = ['x' => 1, 'y' => 2, 'z' => 2, 'a' => 1];
		$xao      = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj      = $xao->merge(['a' => 1, 'z' => 2]);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testMergeAssocArrayAddMultipleArrays()
	{
		$expected = ['x' => 5, 'y' => 2, 'z' => 4, 'a' => 1];
		$xao      = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$obj      = $xao->merge(['a' => 1, 'z' => 2], ['x' => 5, 'z' => 4]);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testMergeAssocArrayAddArrayObject()
	{
		$expected = ['x' => 1, 'y' => 2, 'z' => 2, 'a' => 1];
		$xao1     = new XArray(['x' => 1, 'y' => 2, 'z' => 3]);
		$xao2     = new XArray(['a' => 1, 'z' => 2]);
		$obj      = $xao1->merge($xao2);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testMergeAddScalarValue()
	{
		$expected = [1, 2, 3, 'a', 'z'];
		$xao      = new XArray([1, 2, 3]);
		$obj      = $xao->merge('a', 'z');

		$this->assertEquals($expected, (array) $obj);
	}

	### pop()
	#######################################################

	public function testPopOnNumericArray()
	{
		$xao = new XArray([3, 7]);
		
		$this->assertCount(2, $xao);
		$this->assertSame(7, $xao->pop());
		$this->assertCount(1, $xao);
		$this->assertEquals([3], (array) $xao);
	}

	public function testPopOnAssocArray()
	{
		$xao = new XArray(['x' => 'bar', 'y' => 'foo']);
		
		$this->assertCount(2, $xao);
		$this->assertSame('foo', $xao->pop());
		$this->assertCount(1, $xao);
		$this->assertEquals(['x' => 'bar'], (array) $xao);
	}

	public function testPopOnEmptyArray()
	{
		$xao = new XArray([]);
		
		$this->assertCount(0, $xao);
		$this->assertNull($xao->pop());
	}

	### push()
	#######################################################

	public function testPushReturnsSameInstance()
	{
		$xao = new XArray(['foo']);
		$obj = $xao->push('bar');
		
		$this->assertSame($xao, $obj);
	}

	public function testPushWithSingleElementOnNumericArray()
	{
		$xao = new XArray(['foo']);
		$this->assertCount(1, $xao);
		$xao->push(8);
		
		$this->assertCount(2, $xao);
		$this->assertSame(8,  $xao[1]);
	}

	public function testPushWithSingleElementOnHigherIndexedNumericArray()
	{
		$xao = new XArray([5 => 'foo']);
		$this->assertCount(1, $xao);
		$xao->push(8);
		
		$this->assertCount(2, $xao);
		$this->assertSame(8,  $xao[6]);
	}

	public function testPushWithMultipleElementsOnNumericArray()
	{
		$xao = new XArray(['foo']);
		$this->assertCount(1, $xao);
		$xao->push(8, 'x', true);
		
		$this->assertCount(4,   $xao);
		$this->assertSame(8,    $xao[1]);
		$this->assertSame('x',  $xao[2]);
		$this->assertSame(true, $xao[3]);
	}

	public function testPushWithSingleElementOnAssocArray()
	{
		$xao = new XArray(['foo' => 'bar']);
		$this->assertCount(1, $xao);
		$xao->push(8);
		
		$this->assertCount(2, $xao);
		$this->assertSame(8,  $xao[0]);
	}

	public function testPushWithMultipleElementsOnAssocArray()
	{
		$xao = new XArray(['foo' => 'bar']);
		$this->assertCount(1, $xao);
		$xao->push(8, 'x', true);
		
		$this->assertCount(4,   $xao);
		$this->assertSame(8,    $xao[0]);
		$this->assertSame('x',  $xao[1]);
		$this->assertSame(true, $xao[2]);
	}

	### rand()
	#######################################################

	public function testRandReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->rand();

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testRandArrayLengthValidity()
	{
		$xao  = new XArray([1, 2, 3, 4, 5, 6, 7]);

		$this->assertCount(2, $xao->rand(2));
		$this->assertCount(5, $xao->rand(5));
	}

	// since it’s random, the test may fail by chance
	public function testRandSelectionIsRandom()
	{
		$xao  = new XArray([1, 2, 3, 4, 5, 6, 7]);
		$obj1 = $xao->rand(3);
		$obj2 = $xao->rand(3);

		$this->assertNotEquals($obj1, $obj2);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testRandWithLargerNumThanCountFails()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->rand(5);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testRandWithZeroNumFails()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->rand(0);
	}

	public function invalidNumProvider()
	{
		return [
			[null], [true], [false], [''], ['foo'], [74.46], [-5], [[]], 
		];
	}

	/**
	 * @dataProvider invalidNumProvider
	 * @expectedException InvalidArgumentException
	 */
	public function testRandWithInvalidNumFails($num)
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->rand($num);
	}

	public function testRandWithStringNum()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->rand('2');

		$this->assertCount(2, $obj);
	}

	public function testRandKeysAndValuesExist()
	{
		$xao = new XArray(['foo', 'bar']);
		$obj = $xao->rand();

		$keys = array_keys((array) $obj);
		$this->assertArrayHasKey($keys[0], $obj);

		$values = array_values((array) $obj);
		$this->assertContains($values[0], $obj);
	}

	### reduce()
	#######################################################

	public function testReduceAcceptsFunction()
	{
		$xao   = new XArray;
		$null  = $xao->reduce('test_reduce');

		$this->assertNull($null);
	}

	public function testReduceAcceptsClosure()
	{
		$xao   = new XArray;
		$null  = $xao->reduce(function ($carry, $value) {
			return $carry;
		});

		$this->assertNull($null);
	}

	public function testReduceAcceptsStaticCallback()
	{
		$xao   = new XArray;
		$null  = $xao->reduce(['CallbackTestMethods', 'static_reduce']);

		$this->assertNull($null);
	}

	public function testReduceAcceptsCallback()
	{
		$xao   = new XArray;
		$test  = new CallbackTestMethods;

		$null  = $xao->reduce([$test, 'reduce']);

		$this->assertNull($null);
	}

	/**
     * @depends testReduceAcceptsFunction
     */
	public function testReduceReturnsInitialValueOnEmptyArray()
	{
		$xao    = new XArray;
		$return = $xao->reduce('test_reduce', 'foo');

		$this->assertSame('foo', $return);
	}

	/**
     * @depends testReduceAcceptsFunction
     */
	public function testReduceReturnsFirstValueIfNoInitialGiven()
	{
		$xao    = new XArray([1]);
		$return = $xao->reduce('test_reduce');

		$this->assertSame(1, $return);
	}

	/**
     * @depends testReduceAcceptsFunction
     */
	public function testReduceReturnsDefaultInitialValueIfNoneGivenOnEmptyArray()
	{
		$xao    = new XArray;
		$return = $xao->reduce('test_reduce');

		$this->assertNull($return);
	}

	/**
     * @depends testReduceAcceptsClosure
     */
	public function testReduceSuccess()
	{
		$xao = new XArray([1, 2, 3, 4]);
		// !!! possible NULL => 0 conversion !!!
		// see testReduceSkipsInitialValueIfNoneGiven()
		$sum = $xao->reduce(function ($carry, $value) {
			return $carry += $value;
		});

		$this->assertSame(10, $sum);
	}

	/**
     * @depends testReduceAcceptsClosure
     */
	public function testReduceWalksFromStartToEnd()
	{
		$xao = new XArray([1, 2, 3]);
		$str = $xao->reduce(function ($carry, $value) {
			return $carry .= $value;
		});

		$this->assertSame('123', $str);
	}

	/**
     * @depends testReduceAcceptsClosure
     */
	public function testReduceSkipsInitialValueIfNoneGiven()
	{
		$xao     = new XArray([1, 2, 3]);
		// this would return 0 if the default initial value of NULL gets paased
		$product = $xao->reduce(function ($carry, $value) {
			return $carry *= $value;
		});

		$this->assertSame(6, $product);
	}

	/**
     * @depends testReduceAcceptsClosure
     */
	public function testReduceBindsArrayObjectToClosure()
	{
		try {
			$xao = new XArray([1, 2, 3]);
			$xao->reduce(function ($carry, $value) {
				if (! $this instanceof XArray) {
					throw new \Exception('$this is not an instance of ArrayObject.');
				}
			});
			$this->assertTrue(true);
		}
		catch (\Exception $exc) {
			$this->assertTrue(false, $exc->getMessage());
		}
	}

	### reverse()
	#######################################################

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

	### search()
	#######################################################

	public function testSearchOnNumericArray()
	{
		$xao = new XArray([3, 7]);
		
		$this->assertSame(1, $xao->search(7));
	}

	public function testSearchOnAssocArray()
	{
		$xao = new XArray(['x' => 'bar', 'y' => 'foo']);
		
		$this->assertSame('y', $xao->search('foo'));
	}

	public function testSearchOnEmptyArray()
	{
		$xao = new XArray([]);
		
		$this->assertFalse($xao->search('x'));
	}

	public function testSearchWithNonExistingValue()
	{
		$xao = new XArray([1, 2, 3]);
		
		$this->assertFalse($xao->search('x'));
	}

	public function testSearchWithMultipleMatches()
	{
		$xao = new XArray([3, 7, 5, 7]);
		
		$this->assertSame(1, $xao->search(7));
	}

	public function testSearchInNonStrictMode()
	{
		$xao = new XArray([3, 7]);
		
		$this->assertSame(1, $xao->search(7, false));
		$this->assertSame(1, $xao->search('7', false));
	}

	public function testSearchInStrictMode()
	{
		$xao = new XArray([3, 7]);
		
		$this->assertSame(1, $xao->search(7, true));
		$this->assertFalse($xao->search('7', true));
	}

	public function testSearchStringLookupIsCaseSensitive()
	{
		$xao = new XArray(['foo', 'bar']);
		
		$this->assertFalse($xao->search('Foo'));
	}

	### shift()
	#######################################################

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
	#######################################################

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

	### shuffle()
	#######################################################

	public function testShuffleReturnsArrayObject()
	{
		$xao = new XArray;
		$obj = $xao->shuffle();

		$this->assertSame($xao, $obj);
	}

	public function testShuffleNotChangingLength()
	{
		$xao = new XArray([1, 2, 3]);
		$count_1 = count($xao);
		$xao->shuffle();
		$count_2 = count($xao);

		$this->assertSame($count_1, $count_2);

	}

	public function testShuffleSuccess()
	{
		$expected = range(1, 20);
		$xao = new XArray($expected);
		$xao->shuffle();

		$this->assertNotEquals($expected, (array) $xao);
	}

	public function testShuffleNotChangesArrayElements()
	{
		$expected = [1, 2, 3, 4, 5];
		$xao      = new XArray($expected);
		$array    = (array) $xao->shuffle();
		// undo shuffle
		sort($array, \SORT_NUMERIC);

		$this->assertEquals($expected, $array);
	}

	### unique()
	#######################################################

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
	 * @expectedException RuntimeException
	 */
	public function testUniqueFailsOnConversionProblems()
	{
		$xao = new XArray(['foo', ['bar'], 'bar']);
		$obj = $xao->unique();
	}

	// I couldn’t find an example, where it made a difference
	# public function testUniqueSortVariants(){}

	### values()
	#######################################################

	public function testValuesReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->values();

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testValuesSuccess()
	{
		$expected = [0 => 'bar', 1 => 'y'];
		$xao      = new XArray(['foo' => 'bar', 'x' => 'y']);
		$obj      = $xao->values();

		$this->assertCount(2, $obj);
		$this->assertEquals($expected, (array) $obj);
	}

	### walk()
	#######################################################

	public function testWalkAcceptsFunction()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->walk('test_filter');

		$this->assertSame($xao, $obj);
	}

	public function testWalkAcceptsClosure()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->walk(function ($value) {});

		$this->assertSame($xao, $obj);
	}

	public function testWalkAcceptsStaticCallback()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->walk(['CallbackTestMethods', 'static_filter']);

		$this->assertSame($xao, $obj);
	}

	public function testWalkAcceptsCallback()
	{
		$xao  = new XArray([1, 2, 3]);
		$test = new CallbackTestMethods;
		$obj  = $xao->walk([$test, 'filter']);

		$this->assertSame($xao, $obj);
	}

	/**
	 * @expectedException LogicException
	 */
	public function testWalkFailsForInvalidCallback()
	{
		$xao = new XArray([1, 2]);

		// ArrayObject::count() is non-static and accepts no parameters
		// though the syntax for the callable is correct the execution will fail
		$xao->map(['ArrayObject', 'count']);
	}

	/**
     * @depends testWalkAcceptsClosure
     */
	public function testWalkBindsArrayObjectToClosure()
	{
		try {
			$xao = new XArray([1, 2, 3]);
			$xao->walk(function () {
				if (! $this instanceof XArray) {
					throw new \Exception('$this is not an instance of ArrayObject.');
				}
			});
			$this->assertTrue(true);
		}
		catch (\Exception $exc) {
			$this->assertTrue(false, $exc->getMessage());
		}
	}

	/**
     * @depends testWalkAcceptsClosure
     */
	public function testWalkPassesArrayAsDefaultUserdata()
	{
		try {
			$xao = new XArray([1, 2, 3]);
			$xao->walk(function ($value, $key, $array) {
				if (!is_array($array) and ([1, 2, 3] != $array)) {
					throw new \Exception('Invalid default userdata passed: ' . var_export($array, true));
				}
			});
			$this->assertTrue(true);
		}
		catch (\Exception $exc) {
			$this->assertTrue(false, $exc->getMessage());
		}
	}

	/**
     * @depends testWalkAcceptsClosure
     */
	public function testWalkPassesUserdata()
	{
		try {
			$xao = new XArray([1, 2, 3]);
			$xao->walk(function ($value, $key, $data) {
				if ('foo' !== $data) {
					throw new \Exception('Invalid userdata passed: ' . var_export($data, true));
				}
			}, 'foo');
			$this->assertTrue(true);
		}
		catch (\Exception $exc) {
			$this->assertTrue(false, $exc->getMessage());
		}
	}

	// omitted due to not knowing of a test example
	// (throws runtimeexception)
	// public function testWalkFails() {}

	/**
     * @depends testWalkAcceptsClosure
     */
	public function testWalkModifiesValues()
	{
		$xao      = new XArray([1, 2, 3]);
		$expected = [2, 4, 6];
		$xao->walk(function (&$value) {
			$value = $value * 2;
		});

		$this->assertEquals($expected, (array) $xao);
	}

	/**
     * @depends testWalkAcceptsClosure
     */
	public function testWalkModifiesValuesWithUserdata()
	{
		$xao      = new XArray([1, 2, 3]);
		$expected = [2, 4, 6];
		$xao->walk(function (&$value, $key, $number) {
			$value = $value * $number;
		}, 2);

		$this->assertEquals($expected, (array) $xao);
	}
}