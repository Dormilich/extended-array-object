<?php

use Dormilich\Core\ArrayObject as XArray;

function test_filter() 
{ 
	return true; 
}

function test_map($value) 
{ 
	return $value; 
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
		
		$this->assertInstanceOf($this->classname, $xao->flip());
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
		$xao = new XArray(['bar', 'foo']);
		
		$this->assertCount(2, $xao);
		$this->assertSame('foo', $xao->pop());
		$this->assertCount(1, $xao);
		$this->assertEquals(['bar'], (array) $xao);
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
		$xao = new XArray(['bar', 'foo']);
		
		$this->assertCount(2, $xao);
		$this->assertSame('bar', $xao->shift());
		$this->assertCount(1, $xao);
		$this->assertEquals(['foo'], (array) $xao);
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