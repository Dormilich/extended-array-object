<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class WalkTest extends PHPUnit_Framework_TestCase
{
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
