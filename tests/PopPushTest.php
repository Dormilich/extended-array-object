<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class PopPushTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	### pop()
	###########################################################################

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
		$xao = new XArray(['x' => 'bar', 'y' => 'foo']);
		
		$this->assertCount(2, $xao);
		$this->assertSame('foo', $xao->pop());
		$this->assertCount(1, $xao);
		$this->assertEquals(['x' => 'bar'], (array) $xao);
	}

	public function testPopOnEmptyArray()
	{
		$xao = new XArray([]);
		
		$this->assertCount(0, $xao);
		$this->assertNull($xao->pop());
	}

	### push()
	###########################################################################

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
}
