<?php

use Dormilich\Core\ArrayObject as XArray;

class ArrayObjectTest extends PHPUnit_Framework_TestCase
{
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
		$this->assertInstanceOf('\Dormilich\Core\ArrayObject', $obj);
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
		$this->assertInstanceOf('\Dormilich\Core\ArrayObject', $obj);
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
		$this->assertInstanceOf('\Dormilich\Core\ArrayObject', $obj);
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

		$this->assertInstanceOf('\Dormilich\Core\ArrayObject', $obj);
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
}