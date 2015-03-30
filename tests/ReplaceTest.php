<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class ReplaceTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	public function testReplaceReturnsArrayObject()
	{
		$xao = new XArray;
		$obj = $xao->replace([]);

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotSame($xao, $obj);
	}

	public function testReplaceWithExistingElements()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->replace([1 => 'foo']);

		$this->assertEquals([1, 'foo', 3], (array) $obj);
	}

	public function testReplaceWithNonExistingElements()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->replace(['bar' => 'foo']);

		$this->assertEquals((array) $xao, (array) $obj);
	}

	public function testReplaceWithArrayObject()
	{
		$xao1 = new XArray(['a' => 1, 'b' => 2, 'c' => 3]);
		$xao2 = new XArray(['foo', 'b' => 'y', 'x' => false]);
		$obj  = $xao1->replace($xao2);

		$this->assertEquals(['a' => 1, 'b' => 'y', 'c' => 3], (array) $obj);
	}

	public function testReplaceWithMultipleArrays()
	{
		$xao = new XArray(['a' => 1, 'b' => 2, 'c' => 3]);
		$obj = $xao->replace(['foo' => 'bar', 'c' => 12], ['a' => 0, 1, 2]);

		$this->assertEquals(['a' => 0, 'b' => 2, 'c' => 12], (array) $obj);
	}

	public function testReplaceWithSimilarKeys()
	{
		$xao = new XArray([1, 2, 3]);
		$obj = $xao->replace(['1' => 'foo']);
		// string numbers (int) are automatically converted to integers
		$this->assertEquals([1, 'foo', 3], (array) $obj);
	}
}
