<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class ChangeKeyCaseTest extends PHPUnit_Framework_TestCase
{
	private $classname = '\Dormilich\Core\ArrayObject';

	public function testChangeKeyCaseReturnsArrayObject()
	{
		$xao = new XArray([
			'fOo' => 1, 
			'Bar' => 2, 
		]);
		$obj = $xao->changeKeyCase();

		$this->assertInstanceOf($this->classname, $obj);
		$this->assertNotEquals($xao, $obj);
	}

	public function testChangeKeyCaseToUpper()
	{
		$xao = new XArray([
			'fOo' => 1, 
			'Bar' => 2, 
		]);
		$expected = [
			'FOO' => 1, 
			'BAR' => 2, 
		];
		$obj = $xao->changeKeyCase(\CASE_UPPER);
		$this->assertEquals($expected, (array) $obj);
	}

	public function testChangeKeyCaseToLower()
	{
		$xao = new XArray([
			'fOo' => 1, 
			'Bar' => 2, 
		]);
		$expected = [
			'foo' => 1, 
			'bar' => 2, 
		];
		$obj = $xao->changeKeyCase(\CASE_LOWER);
		$this->assertEquals($expected, (array) $obj);
	}

	public function testChangeKeyCaseToTitle()
	{
		$xao = new XArray([
			'fOo' => 1, 
			'Bar' => 2, 
		]);
		$expected = [
			'Foo' => 1, 
			'Bar' => 2, 
		];
		$obj = $xao->changeKeyCase(\MB_CASE_TITLE);
		$this->assertEquals($expected, (array) $obj);
	}

	public function testChangeKeyCaseUsingDefault()
	{
		$xao = new XArray([
			'fOo' => 1, 
			'Bar' => 2, 
		]);
		$expected = [
			'foo' => 1, 
			'bar' => 2, 
		];
		$obj = $xao->changeKeyCase();
		$this->assertEquals($expected, (array) $obj);
	}

	public function invalidCaseConstantProvider()
	{
		return [['foo'], [29], [8.7], [null], ['']];
	}

	/**
     * @dataProvider invalidCaseConstantProvider
     */
	public function testChangeKeyCaseUsingBogusParameter($param)
	{
		// array_change_key_case() uses CASE_UPPER for integers that do not 
		// match one of the constants
		$xao = new XArray([
			'fOo' => 1, 
			'Bar' => 2, 
		]);
		$expected = [
			'foo' => 1, 
			'bar' => 2, 
		];
		$obj = $xao->changeKeyCase($param);

		$this->assertEquals($expected, (array) $obj);
	}

	public function testChangeKeyCaseMultibyteKeys()
	{
		$xao = new XArray([
			'cœur'  => 1, 
			'Søren' => 2, 
		]);
		$expected = [
			'CŒUR'  => 1, 
			'SØREN' => 2, 
		];
		$obj = $xao->changeKeyCase(\CASE_UPPER);

		$this->assertEquals($expected, (array) $obj);
	}
}