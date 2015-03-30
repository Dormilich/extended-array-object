<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class SearchTest extends PHPUnit_Framework_TestCase
{
	public function testSearchOnNumericArray()
	{
		$xao = new XArray([3, 7]);
		
		$this->assertSame(1, $xao->search(7));
	}

	public function testSearchOnAssocArray()
	{
		$xao = new XArray(['x' => 'bar', 'y' => 'foo']);
		
		$this->assertSame('y', $xao->search('foo'));
	}

	public function testSearchOnEmptyArray()
	{
		$xao = new XArray([]);
		
		$this->assertFalse($xao->search('x'));
	}

	public function testSearchWithNonExistingValue()
	{
		$xao = new XArray([1, 2, 3]);
		
		$this->assertFalse($xao->search('x'));
	}

	public function testSearchWithMultipleMatches()
	{
		$xao = new XArray([3, 7, 5, 7]);
		
		$this->assertSame(1, $xao->search(7));
	}

	public function testSearchInNonStrictMode()
	{
		$xao = new XArray([3, 7]);
		
		$this->assertSame(1, $xao->search(7, false));
		$this->assertSame(1, $xao->search('7', false));
	}

	public function testSearchInStrictMode()
	{
		$xao = new XArray([3, 7]);
		
		$this->assertSame(1, $xao->search(7, true));
		$this->assertFalse($xao->search('7', true));
	}

	public function testSearchStringLookupIsCaseSensitive()
	{
		$xao = new XArray(['foo', 'bar']);
		
		$this->assertFalse($xao->search('Foo'));
	}
}
