<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class MapTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

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
	 * @expectedException RuntimeException
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
}
