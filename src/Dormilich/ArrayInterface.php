<?php
// ArrayInterface.php

namespace Dormilich;

interface ArrayInterface
{
	/**
	 * @var integer
	 */
	const USE_VALUE = 1;
	/**
	 * @var integer
	 */
	const USE_KEY   = 2;
	/**
	 * Returns an array with all keys from array lowercased or uppercased. 
	 * Numbered indices are left as is. 
	 * 
	 * @param integer $case Either CASE_UPPER or CASE_LOWER (default).
	 * @return ArrayObject Returns an array with its keys lower or uppercased.
	 */
	public function changeKeyCase($case = \CASE_LOWER);
	public function chunk($size, $preserve_keys = false);
	public function contains($needle, $strict = false);
	public function countValues();
	public function diff($input, $mode = ArrayInterface::USE_VALUE);
	public function udiff($input, $compare_func, $mode = ArrayInterface::USE_VALUE);
	public function filter($callback); // flag
	public function flip();
	public function intersect($input, $mode = ArrayInterface::USE_VALUE);
	public function uintersect($input, $compare_func, $mode = ArrayInterface::USE_VALUE);
	public function keys(); // search_value, strict
	public function map($callback);
	public function merge($input);
	public function pop();
	public function rand($num = 1);
	public function reduce($callback, $initial = NULL);
	public function replace($input);
	public function reverse($preserve_keys = false);
	public function search($search_value, $strict = false);
	public function shift();
	public function shuffle();
	public function slice ($offset, $length = NULL, $preserve_keys = false);
	public function sort($sort_flags = \SORT_REGULAR, $mode = ArrayInterface::USE_VALUE, $preserve_keys = false);
	public function rsort($sort_flags = \SORT_REGULAR, $mode = ArrayInterface::USE_VALUE, $preserve_keys = false);
	public function usort($callback, $mode = ArrayInterface::USE_VALUE, $preserve_keys = false);
	public function splice($offset); // length, replacement
	public function unique ($sort_flags = \SORT_STRING);
	public function unshift($input);
	public function values();
	public function walk ($callback, $userdata = NULL);
}