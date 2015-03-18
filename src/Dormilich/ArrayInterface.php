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
	/**
	 * Returns an array using the values of the array as keys and their 
	 * frequency in the array as values. 
	 * 
	 * @return ArrayObject Returns an associative array of values from array 
	 *          as keys and their count as value. 
	 */
	public function countValues();
	public function diff($input, $mode = ArrayInterface::USE_VALUE);
	public function udiff($input, $compare_func, $mode = ArrayInterface::USE_VALUE);
	/**
	 * Iterates over each value in the array passing them to the callback 
	 * function. If the callback function returns true, the current value from 
	 * the array is returned into the result array. Array keys are preserved. 
	 * 
	 * @param callable $callback The callback function to use.
	 * @return ArrayObject Returns the filtered array. 
	 */
	public function filter(callable $callback);
	/**
	 * Exchanges all keys with their associated values in the array.
	 * 
	 * Note that the values of array need to be valid keys, i.e. they need to 
	 * be either integer or string. A warning will be emitted if a value has 
	 * the wrong type, and the key/value pair in question will not be included 
	 * in the result. 
	 * 
	 * If a value has several occurrences, the latest key will be used as its 
	 * value, and all others will be lost. 
	 * 
	 * @return ArrayObject Returns the flipped array.
	 * @throws Exception Failed to flip the array.
	 */
	public function flip();
	public function intersect($input, $mode = ArrayInterface::USE_VALUE);
	public function uintersect($input, $compare_func, $mode = ArrayInterface::USE_VALUE);
	public function keys(); // search_value, strict
	/**
	 * Returns an array containing all the elements of the array after 
	 * applying the callback function to each one.
	 * 
	 * Note: this implementation differs from array_map() with regards to the 
	 * parameters passed to the callback.
	 * 
	 * @param callable $callback Callback function to run for each element 
	 *          in each array. Receives the element’s value and key as parameters.
	 * @return ArrayObject Returns an array containing all the elements of the 
	 *          array after applying the callback function to each one. 
	 */
	public function map(callable $callback);
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