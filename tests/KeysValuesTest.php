<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class KeysValuesTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	### keys()
	###########################################################################

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

	### values()
	###########################################################################

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
}
