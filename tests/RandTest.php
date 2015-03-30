<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class RandTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

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
		$xao  = new XArray(range(1, 20));
		$obj1 = $xao->rand(10);
		$obj2 = $xao->rand(10);

		$this->assertNotEquals($obj1, $obj2, 'This test might have failed by probability.');
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
}
