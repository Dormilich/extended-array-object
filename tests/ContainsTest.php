<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class ContainsTest extends PHPUnit_Framework_TestCase
{
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