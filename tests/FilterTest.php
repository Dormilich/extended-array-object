<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class FilterTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

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
		$xao      = new XArray([2 => 3, 5 => 1, 7 => 9]);
		$expected = [2 => 3, 7 => 9];

		$obj      = $xao->filter(function ($value, $key) { 
			return ($value > $key); 
		});

		$this->assertEquals($expected, (array) $obj);
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
}