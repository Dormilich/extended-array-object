<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class JoinTest extends PHPUnit_Framework_TestCase
{
	public function testJoinReturnsString()
	{
		$xao = new XArray;

		$this->assertInternalType('string', $xao->join());
	}

	public function testJoinWithScalarArrayWithoutGlue()
	{
		$xao = new XArray([13, 2.87, null, 'foo', true, false]);

		$this->assertSame('132.87foo1', $xao->join());
	}

	public function testJoinWithScalarArrayWithGlue()
	{
		$xao = new XArray([13, 2.87, null, 'foo', true, false]);

		$this->assertSame('13, 2.87, , foo, 1, ', $xao->join(', '));
	}

	/**
	 * @expectedException ErrorException
	 */
	public function testJoinWithNonscalarArray()
	{
		$xao = new XArray([1, ['foo'], 7]);

		$xao->join('-');
	}
}
