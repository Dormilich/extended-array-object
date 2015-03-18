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
	/**
	 * Chunks an array into arrays with {size} elements. The last chunk may 
	 * contain less than {size} elements.  
	 * 
	 * @param integer $size The size of each chunk.
	 * @param boolean $preserve_keys When set to TRUE keys will be preserved. 
	 * 			Default is FALSE which will reindex the chunk numerically.
	 * @return ArrayObject Returns a multidimensional numerically indexed 
	 *			array, starting with zero, with each dimension containing size 
	 *			elements. 
	 */
	public function chunk($size, $preserve_keys = false);
	/**
	 * Checks if a value exists in the array using loose comparison unless 
	 * strict is set.
	 * 
	 * @param mixed $needle The searched value.
	 * @param bool $strict If the parameter strict is set to TRUE then method 
	 *          will also check the types of the needle in the array.
	 * @return boolean Returns TRUE if needle is found in the array, FALSE otherwise. 
	 */
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