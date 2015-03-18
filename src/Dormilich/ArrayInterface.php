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
	/**
	 * Pops and returns the last value of the array, shortening the array by 
	 * one element. 
	 * 
	 * @return mixed Returns the last value of the array. If the array is 
	 * 			empty, NULL will be returned. 
	 */
	public function pop();
	/**
	 * Push one or more elements onto the end of the array.
	 * 
	 * @param mixed $value The first value to push onto the end of the array. 
	 * @return ArrayObject Returns the array.
	 */
	public function push($value);
	/**
	 * Picks one or more random entries out of an array, and returns them.
	 * If {$num} is greater than the array length, the complete array and if 
	 * it is 0 an empty array will be returned.
	 * 
	 * @param integer $num Specifies how many entries should be picked. 
	 * @return ArrayObject Returns an array of the selected entries.
	 */
	public function rand($num = 1);
	/**
	 * Applies iteratively the callback function to the elements of the array, 
	 * so as to reduce the array to a single value. 
	 * 
	 * @param callable $callback 
	 *          mixed callback ( mixed $carry , $mixed $item )
	 *          > carry Holds the return value of the previous iteration; in 
	 *                  the case of the first iteration it instead holds the 
	 *                  value of initial. 
	 *          > item  Holds the value of the current iteration.  
	 * @param mixed $initial It will be used at the beginning of the process, 
	 *          or as a final result in case the array is empty. 
	 * @return mixed Returns the resulting value. 
	 */
	public function reduce(callable $callback, $initial = NULL);
	public function replace($input);
	/**
	 * Return an array with elements in reverse order.
	 * 
	 * @param boolean $preserve_keys If set to TRUE numeric keys are preserved. 
	 *          Non-numeric keys are not affected by this setting and will 
	 *          always be preserved. 
	 * @return ArrayObject Returns the reversed array. 
	 */
	public function reverse($preserve_keys = false);
	public function search($search_value, $strict = false);
	/**
	 * Shifts the first value of the array off and returns it, shortening the 
	 * array by one element and moving everything down. All numerical array 
	 * keys will be modified to start counting from zero while literal keys 
	 * won’t be touched. 
	 * 
	 * @return mixed Returns the shifted value, or NULL if the array is empty.
	 */
	public function shift();
	/**
	 * Shuffles (randomizes the order of the elements in) the array. 
	 * 
	 * @return ArrayObject Returns the array.
	 */
	public function shuffle();
	/**
	 * Returns the sequence of elements from the array array as specified by 
	 * the {offset} and {length} parameters. 
	 * 
	 * @param integer $offset If offset is non-negative, the sequence will 
	 *          start at that offset in the array. If offset is negative, the 
	 *          sequence will start that far from the end of the array. 
	 * @param integer $length If length is given and is positive, then the 
	 *          sequence will have up to that many elements in it. If the 
	 *          array is shorter than the length, then only the available 
	 *          array elements will be present. If length is given and is 
	 *          negative then the sequence will stop that many elements from 
	 *          the end of the array. If it is omitted, then the sequence will 
	 *          have everything from offset up until the end of the array. 
	 * @param boolean $preserve_keys Note that slice() will reorder and reset 
	 *          the numeric array indices by default. You can change this 
	 *          behaviour by setting preserve_keys to TRUE. 
	 * @return ArrayObject Returns the slice. 
	 */
	public function slice ($offset, $length = NULL, $preserve_keys = false);
	public function sort($sort_flags = \SORT_REGULAR, $mode = ArrayInterface::USE_VALUE, $preserve_keys = false);
	public function rsort($sort_flags = \SORT_REGULAR, $mode = ArrayInterface::USE_VALUE, $preserve_keys = false);
	public function usort($callback, $mode = ArrayInterface::USE_VALUE, $preserve_keys = false);
	/**
	 * Removes the elements designated by offset and length from the input 
	 * array, and replaces them with the elements of the replacement array, 
	 * if supplied. 
	 * 
	 * Note that numeric keys in the array are not preserved. 
	 * 
	 * @param integer $offset If offset is positive then the start of removed 
	 * 			portion is at that offset from the beginning of the array. If 
	 * 			offset is negative then it starts that far from the end of the array. 
	 * @param integer $length If length is omitted, removes everything from 
	 * 			offset to the end of the array. If length is specified and is 
	 * 			positive, then that many elements will be removed. If length 
	 * 			is specified and is negative then the end of the removed 
	 * 			portion will be that many elements from the end of the array. 
	 * @param array $replacement If replacement array is specified, then the 
	 * 			removed elements are replaced with elements from this array. 
	 * 			If replacement is not an array, it will be typecast to one.
	 * 			If offset and length are such that nothing is removed, then 
	 * 			the elements from the replacement array are inserted in the 
	 * 			place specified by the offset. Note that keys in replacement 
	 * 			array are not preserved. 
	 * @return ArrayObject Returns the array consisting of the extracted elements. 
	 */
	public function splice($offset);
	/**
	 * returns a new array without duplicate values. 
	 * 
	 * Note that keys are preserved. unique() sorts the values treated as 
	 * string at first, then will keep the first key encountered for every 
	 * value, and ignore all following keys. It does not mean that the key 
	 * of the first related value from the unsorted array will be kept. 
	 * 
	 * Two elements are considered equal if and only if 
	 * (string) $elem1 === (string) $elem2 i.e. when the string 
	 * representation is the same, the first element will be used. 
	 * 
	 * @param integer $sort_flags The optional second parameter sort_flags 
	 *          may be used to modify the sorting behavior.
	 * @return ArrayObject Returns the filtered array. 
	 */
	public function unique($sort_flags = \SORT_STRING);
	/**
	 * Prepends passed elements to the front of the array. Note that the list 
	 * of elements is prepended as a whole, so that the prepended elements stay 
	 * in the same order. All numerical array keys will be modified to start 
	 * counting from zero while literal keys won’t be touched. 
	 * 
	 * @param mixed $value First value to prepend.  
	 * @return ArrayObject Returns the array.
	 */
	public function unshift($value);
	public function values();
	public function walk ($callback, $userdata = NULL);
}