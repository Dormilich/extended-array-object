<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class ReduceTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	public function testReduceAcceptsFunction()
	{
		$xao   = new XArray;
		$null  = $xao->reduce('test_reduce');

		$this->assertNull($null);
	}

	public function testReduceAcceptsClosure()
	{
		$xao   = new XArray;
		$null  = $xao->reduce(function ($carry, $value) {
			return $carry;
		});

		$this->assertNull($null);
	}

	public function testReduceAcceptsStaticCallback()
	{
		$xao   = new XArray;
		$null  = $xao->reduce(['CallbackTestMethods', 'static_reduce']);

		$this->assertNull($null);
	}

	public function testReduceAcceptsCallback()
	{
		$xao   = new XArray;
		$test  = new CallbackTestMethods;

		$null  = $xao->reduce([$test, 'reduce']);

		$this->assertNull($null);
	}

	/**
	 * @depends testReduceAcceptsFunction
	 */
	public function testReduceReturnsInitialValueOnEmptyArray()
	{
		$xao    = new XArray;
		$return = $xao->reduce('test_reduce', 'foo');

		$this->assertSame('foo', $return);
	}

	/**
	 * @depends testReduceAcceptsFunction
	 */
	public function testReduceReturnsFirstValueIfNoInitialGiven()
	{
		$xao    = new XArray([1]);
		$return = $xao->reduce('test_reduce');

		$this->assertSame(1, $return);
	}

	/**
	 * @depends testReduceAcceptsFunction
	 */
	public function testReduceReturnsDefaultInitialValueIfNoneGivenOnEmptyArray()
	{
		$xao    = new XArray;
		$return = $xao->reduce('test_reduce');

		$this->assertNull($return);
	}

	/**
	 * @depends testReduceAcceptsClosure
	 */
	public function testReduceSuccess()
	{
		$xao = new XArray([1, 2, 3, 4]);
		// !!! possible NULL => 0 conversion !!!
		// see testReduceSkipsInitialValueIfNoneGiven()
		$sum = $xao->reduce(function ($carry, $value) {
			return $carry += $value;
		});

		$this->assertSame(10, $sum);
	}

	/**
	 * @depends testReduceAcceptsClosure
	 */
	public function testReduceWalksFromStartToEnd()
	{
		$xao = new XArray([1, 2, 3]);
		$str = $xao->reduce(function ($carry, $value) {
			return $carry .= $value;
		});

		$this->assertSame('123', $str);
	}

	/**
	 * @depends testReduceAcceptsClosure
	 */
	public function testReduceSkipsInitialValueIfNoneGiven()
	{
		$xao     = new XArray([1, 2, 3]);
		// this would return 0 if the default initial value of NULL gets paased
		$product = $xao->reduce(function ($carry, $value) {
			return $carry *= $value;
		});

		$this->assertSame(6, $product);
	}

	/**
	 * @depends testReduceSkipsInitialValueIfNoneGiven
	 */
	public function testReduceCanUseArrayKeysInCallback()
	{
		$xao     = new XArray(['a' => 1, 'b' => 2, 'c' => 3]);
		// this would return 0 if the default initial value of NULL gets paased
		$string = $xao->reduce(function ($carry, $value, $key) {
			return $carry .= $key . ':' . $value . '|';
		}, '|');

		$this->assertSame('|a:1|b:2|c:3|', $string);
	}
}
