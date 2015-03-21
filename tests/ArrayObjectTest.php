<?php

use Dormilich\Core\ArrayObject as XArray;

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
		$this->assertEquals($expected, $obj->getArrayCopy());

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
		$this->assertEquals($expected, $obj->getArrayCopy());

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
		$this->assertEquals($expected, $obj->getArrayCopy());

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
		$this->assertEquals($expected, $obj->getArrayCopy());
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

		function test() { return true; }

		$xao->filter('test');

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

	/**
     * @depends testFilterAcceptsClosure
     */
	public function testFilterReturnsArrayObject()
	{
		$xao = new XArray([1, 2, 3]);

		$obj = $xao->filter(function ($value) { return true; });

		$this->assertInstanceOf($this->classname, $obj);
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
     * @depends testCountableInterfaceExists
     * @depends testFilterAcceptsClosure
     */
	public function testFilterBindsArrayObjectToClosure()
	{
		$array = [1, 2, 3];
		$xao   = new XArray($array);

		$xao->filter(function () {
			return (bool) $this->count();
		});

		$this->assertEquals($array, (array) $xao);
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
	}

	public function testFlipNumericValues()
	{
		$array    = [1 => 'foo', 3 => 'bar'];
		$expected = ['foo' => 1, 'bar' => 3];
		$xao      = new XArray($array);
		
		$this->assertEquals($expected, $xao->flip()->getArrayCopy());
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

	### pop()
	#######################################################

	/**
     * @depends testCountableInterfaceExists
     */
	public function testPopOnNumericArray()
	{
		$xao = new XArray([3, 7]);
		
		$this->assertSame(7, $xao->pop());
		$this->assertCount(1, $xao);
		$this->assertEquals([3], $xao->getArrayCopy());
	}

	/**
     * @depends testCountableInterfaceExists
     */
	public function testPopOnAssocArray()
	{
		$xao = new XArray(['bar', 'foo']);
		
		$this->assertSame('foo', $xao->pop());
		$this->assertCount(1, $xao);
		$this->assertEquals(['bar'], $xao->getArrayCopy());
	}

	/**
     * @depends testCountableInterfaceExists
     */
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

	/**
     * @depends testCountableInterfaceExists
     * @depends testArrayAccessInterfaceExists
     */
	public function testPushWithSingleElementOnNumericArray()
	{
		$xao = new XArray(['foo']);
		$this->assertCount(1, $xao);
		$xao->push(8);
		
		$this->assertCount(2, $xao);
		$this->assertSame(8,  $xao[1]);
	}

	/**
     * @depends testCountableInterfaceExists
     * @depends testArrayAccessInterfaceExists
     */
	public function testPushWithSingleElementOnHigherIndexedNumericArray()
	{
		$xao = new XArray([5 => 'foo']);
		$this->assertCount(1, $xao);
		$xao->push(8);
		
		$this->assertCount(2, $xao);
		$this->assertSame(8,  $xao[6]);
	}

	/**
     * @depends testCountableInterfaceExists
     * @depends testArrayAccessInterfaceExists
     */
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

	/**
     * @depends testCountableInterfaceExists
     * @depends testArrayAccessInterfaceExists
     */
	public function testPushWithSingleElementOnAssocArray()
	{
		$xao = new XArray(['foo' => 'bar']);
		$this->assertCount(1, $xao);
		$xao->push(8);
		
		$this->assertCount(2, $xao);
		$this->assertSame(8,  $xao[0]);
	}

	/**
     * @depends testCountableInterfaceExists
     * @depends testArrayAccessInterfaceExists
     */
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
}