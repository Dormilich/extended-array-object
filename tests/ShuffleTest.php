<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class ShuffleTest extends PHPUnit_Framework_TestCase
{
	public function testShuffleReturnsArrayObject()
	{
		$xao = new XArray;
		$obj = $xao->shuffle();

		$this->assertSame($xao, $obj);
	}

	public function testShuffleNotChangingLength()
	{
		$xao = new XArray([1, 2, 3]);
		$count_1 = count($xao);
		$xao->shuffle();
		$count_2 = count($xao);

		$this->assertSame($count_1, $count_2);

	}

	public function testShuffleSuccess()
	{
		$expected = range(1, 20);
		$xao = new XArray($expected);
		$xao->shuffle();

		$this->assertNotEquals($expected, (array) $xao);
	}

	public function testShuffleNotChangesArrayElements()
	{
		$expected = [1, 2, 3, 4, 5];
		$xao      = new XArray($expected);
		$array    = (array) $xao->shuffle();
		// undo shuffle
		sort($array, \SORT_NUMERIC);

		$this->assertEquals($expected, $array);
	}
}
