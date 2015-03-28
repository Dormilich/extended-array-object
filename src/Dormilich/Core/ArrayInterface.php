<?php
// ArrayInterface.php

namespace Dormilich\Core;

interface ArrayInterface
{
	/**
	 * @var integer Compare using the array values.
	 */
	const COMPARE_VALUE = 1;

	/**
	 * @var integer Compare using the array keys.
	 */
	const COMPARE_KEY   = 2;

	/**
	 * Returns an array with all keys from array lowercased or uppercased. 
	 * Numbered indices are left as is. 
	 * 
	 * @param integer $case Either CASE_UPPER or CASE_LOWER (default).
	 * @return ArrayObject Returns an array with its keys lower or uppercased.
	 */
	public function changeKeyCase($case = \CASE_LOWER);

	/**
	 * Merges the elements of the array with one or more arrays together so 
	 * that the values of one are appended to the end of the previous one. 
	 * It returns the resulting array. 
	 * 
	 * If the input arrays have the same string keys, then the later value for 
	 * that key will overwrite the previous one. If, however, the arrays 
	 * contain numeric keys, the later value will not overwrite the original 
	 * value, but will be appended. 
	 * 
	 * Values in the input array with numeric keys will be renumbered with 
	 * incrementing keys starting from zero in the result array.
	 * 
	 * @param mixed $input First array to merge. 
	 * @return ArrayObject Returns the resulting array. 
	 */
	public function concat($input);

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

	/**
	 * Compares the array against one or more other arrays and returns the 
	 * elements in the array whose values are not present in any of the other 
	 * arrays. 
	 * 
	 * If input is not an array it will be converted to an array. 
	 * 
	 * @param mixed $input (multiple) First array to compare against.
	 * @param callable $callback (optional) A function that compares the 
	 * 			array values.
	 * @return ArrayObject Returns an array containing all the entries from 
	 * 			the array that are not present in any of the other arrays. 
	 */
	public function diff($input);

	/**
	 * Compares the array against one or more other arrays and returns the 
	 * elements in the array whose keys are not present in any of the other 
	 * arrays. 
	 * 
	 * If input is not an array, it will be converted to array keys.
	 * 
	 * @param mixed $input (multiple) First array to compare against.
	 * @param callable $callback (optional) A function that compares the 
	 * 			array keys.
	 * @return ArrayObject Returns an array containing all the entries from 
	 * 			the array that are not present in any of the other arrays. 
	 * @throws RuntimeException Input cannot be converted to array keys.
	 */
	public function kdiff($input);

	/**
	 * Compares the array against one or more other arrays and returns the 
	 * elements that are not present in any of the other arrays using the keys 
	 * and values for comparison.
	 * 
	 * @param mixed $input (multiple) The first array to compare against.
	 * @param callable|null $value_compare_func (optional) Function to compare 
	 * 			the array values.
	 * @param callable|null $key_compare_func (optional) Function to compare 
	 * 			the array keys.
	 * @return ArrayObject Returns an array containing all the entries from 
	 * 			the array that are not present in any of the other arrays. 
	 */
	public function adiff($input);

	/**
	 * Compares the input against the array object and returns the elements 
	 * in the input whose values are not present in the array object. This is 
	 * the reverse method to ArrayObject::diff().
	 * 
	 * @param mixed $input An array that is comared against the array object. 
	 * @param callable $callback (optional) A function that compares the 
	 * 			array values.
	 * @return ArrayObject The diff between input and array object.
	 * @throws RuntimeException Input cannot be converted to array.
	 */
	public function xdiff($input, callable $callback = null);

	/**
	 * Compares the input against the array object and returns the elements 
	 * in the input whose keys are not present in the array object. This is 
	 * the reverse method to ArrayObject::kdiff().
	 * 
	 * @param mixed $input An array that is comared against the array object. 
	 * @param callable $callback (optional) A function that compares the 
	 * 			array keys.
	 * @return ArrayObject The key diff between input and array object.
	 * @throws RuntimeException Input cannot be converted to array.
	 */
	public function xkdiff($input, callable $callback = null);

	/**
	 * Compares the input against the array object and returns the elements 
	 * in the input whose values are not present in the array object. This is 
	 * the reverse method to ArrayObject::diff().
	 * 
	 * @param mixed $input An array that is comared against the array object. 
	 * @param callable|null $value_compare_func (optional) Function to compare 
	 * 			the array values.
	 * @param callable|null $key_compare_func (optional) Function to compare 
	 * 			the array keys.
	 * @return ArrayObject The diff between input and array object.
	 * @throws RuntimeException Input cannot be converted to array.
	 */
	public function xadiff($input);

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
	 * @throws RuntimeException Failed to flip the array.
	 */
	public function flip();

	/**
	 * Returns an array containing all the values of the array that are 
	 * present in all the arguments. Note that keys are preserved. 
	 * 
	 * @param mixed $input (multiple) First array to compare against.
	 * @param callable $callback (optional) A function that compares the 
	 * 			array values.
	 * @return ArrayObject Returns an array containing all the entries from 
	 * 			the array that are present in all of the other arrays. 
	 */
	public function intersect($input);

	/**
	 * Compares the array against one or more other arrays and returns the 
	 * elements in the array whose keys are present in all of the other 
	 * arrays. 
	 * 
	 * If input is not an array, it will be converted to array keys.
	 * 
	 * @param mixed $input (multiple) First array to compare against.
	 * @param callable $callback (optional) A function that compares the 
	 * 			array keys.
	 * @return ArrayObject Returns an array containing all the entries from 
	 * 			the array that are present in all of the other arrays. 
	 * @throws RuntimeException Input cannot be converted to array keys.
	 */
	public function kintersect($input);

	/**
	 * Computes the intersection of arrays by using callback function(s) for 
	 * comparison. 
	 * 
	 * @param mixed $input (multiple) The first array to compare against.
	 * @param callable|null $value_compare_func (optional) Function to compare 
	 * 			the array values.
	 * @param callable|null $key_compare_func (optional) Function to compare 
	 * 			the array keys.
	 * @param integer $mode (conditional) If only one callback is given, this 
	 * 			flag determines whether it should be used for value or key 
	 * 			comparison. Can be either of ArrayInterface::COMPARE_VALUE or 
	 * 			ArrayInterface::COMPARE_KEY.
	 * @return ArrayObject Returns an array containing all the entries from 
	 * 			the array that are present in all of the other arrays. 
	 */
	public function aintersect($input);

	/**
	 * Join the array’s elements with a string.
	 * 
	 * @param string $glue Defaults to an empty string. 
	 * @return string Returns a string containing a string representation of 
	 *          all the array elements in the same order, with the glue string 
	 *          between each element. 
	 */
	public function join($glue = '');

	/**
	 * Return all the keys or a subset of the keys of the array.
	 * 
	 * @param mixed $search_value If specified, then only keys containing 
	 * 			these values are returned. 
	 * @param boolean $strict Determines if strict comparison (===) should be 
	 * 			used during the search. 
	 * @return ArrayObject Returns an array of all the (specified) keys in the array.  
	 */
	public function keys();

	/**
	 * Returns an array containing all the elements of the array after 
	 * applying the callback function to each one.
	 * 
	 * Note: this implementation differs from array_map() with regards to the 
	 * parameters passed to the callback.
	 * 
	 * @param callable $callback Callback function to run for each element 
	 *          in each array. Receives the element’s value and key as parameters.
	 * @param boolean $preserve_keys When set to TRUE keys will be preserved. 
	 * 			Default is FALSE which will reindex the array numerically.
	 * @return ArrayObject Returns an array containing all the elements of the 
	 *          array after applying the callback function to each one. 
	 */
	public function map(callable $callback, $preserve_keys = false);

	/**
	 * Replaces the values of the array with values having the same keys in 
	 * each of the following arrays. If a key from the first array exists in 
	 * the second array, its value will be replaced by the value from the 
	 * second array. If the key exists in the second array, and not the first, 
	 * it will be created in the first array. If a key only exists in the first 
	 * array, it will be left as is. If several arrays are passed for replacement, 
	 * they will be processed in order, the later arrays overwriting the previous 
	 * values. 
	 * 
	 * @param mixed $input The first array from which elements will be extracted. 
	 * @return ArrayObject Returns an array on success.
	 * @throws RuntimeException An error occurred.
	 */
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
	 *                  value of the first element. 
	 *          > item  Holds the value of the current iteration.  
	 * @param mixed $initial It will be used at the beginning of the process, 
	 *          or as a final result in case the array is empty. 
	 * @return mixed Returns the resulting value. 
	 */
	public function reduce(callable $callback, $initial = NULL);

	/**
	 * Replaces the values of the array with values having the same keys in 
	 * each of the following arrays. If a key from the first array exists in 
	 * the second array, its value will be replaced by the value from the 
	 * second array. If a key only exists in the first array, it will be left 
	 * as is. If several arrays are passed for replacement, they will be 
	 * processed in order, the later arrays overwriting the previous values. 
	 * 
	 * @param mixed $input The array from which elements will be extracted. 
	 * @return ArrayObject Returns an array on success.
	 * @throws RuntimeException An error occurred.
	 */
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

	/**
	 * Searches the array for a given value and returns the corresponding key 
	 * if successful.
	 * 
	 * @param mixed $search_value The searched value. If search_value is a 
	 * 			string, the comparison is done in a case-sensitive manner. 
	 * @param boolean $strict If the third parameter strict is set to TRUE 
	 * 			then the search() method will search for identical elements 
	 * 			in the array. This means it will also check the types of the 
	 * 			search_value in the array, and objects must be the same instance. 
	 * @return mixed Returns the key for search_value if it is found in the 
	 * 			array, FALSE otherwise. 
	 */
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

	/**
	 * This method sorts the array. Elements will be arranged from lowest to 
	 * highest when this function has completed. 
	 * 
	 * @param integer $sort_flags The optional second parameter sort_flags may 
	 *          be used to modify the sorting behavior using these values: 
	 *           · SORT_REGULAR - compare items normally (don't change types)
	 *           · SORT_NUMERIC - compare items numerically
	 *           · SORT_STRING - compare items as strings 
	 *           · SORT_LOCALE_STRING - compare items as strings, based on the 
	 *                  current locale. It uses the locale, which can be 
	 *                  changed using setlocale() 
	 *           · SORT_NATURAL - compare items as strings using "natural 
	 *                  ordering" like natsort() 
	 *           · SORT_FLAG_CASE - can be combined (bitwise OR) with SORT_STRING 
	 *                  or SORT_NATURAL to sort strings case-insensitively 
	 * @param integer $mode Determines whether the comparison should be made 
	 * 			on the array values or the array keys using the 
	 * 			ArrayInterface::COMPARE_VALUE and ArrayInterface::COMPARE_KEY constants.
	 * @param boolean $preserve_keys If this parameter is set to TRUE the array 
	 * 			indices maintain their correlation with the array elements they 
	 * 			are associated with. This is used mainly when sorting associative 
	 * 			arrays where the actual element order is significant. 
	 * @return ArrayObject Returns the array on success.
	 * @throws Exception Sorting failed.
	 */
	public function sort($sort_flags = \SORT_REGULAR, $mode = ArrayInterface::COMPARE_VALUE, $preserve_keys = false);

	/**
	 * This method sorts the array. Elements will be arranged from highest to 
	 * lowest (reverse order) when this function has completed. 
	 * 
	 * @param integer $sort_flags The optional second parameter sort_flags may 
	 *          be used to modify the sorting behavior using these values: 
	 *           · SORT_REGULAR - compare items normally (don't change types)
	 *           · SORT_NUMERIC - compare items numerically
	 *           · SORT_STRING - compare items as strings 
	 *           · SORT_LOCALE_STRING - compare items as strings, based on the 
	 *                  current locale. It uses the locale, which can be 
	 *                  changed using setlocale() 
	 *           · SORT_NATURAL - compare items as strings using "natural 
	 *                  ordering" like natsort() 
	 *           · SORT_FLAG_CASE - can be combined (bitwise OR) with SORT_STRING 
	 *                  or SORT_NATURAL to sort strings case-insensitively 
	 * @param integer $mode Determines whether the comparison should be made 
	 * 			on the array values or the array keys using the 
	 * 			ArrayInterface::COMPARE_VALUE and ArrayInterface::COMPARE_KEY constants.
	 * @param boolean $preserve_keys If this parameter is set to TRUE the array 
	 * 			indices maintain their correlation with the array elements they 
	 * 			are associated with. This is used mainly when sorting associative 
	 * 			arrays where the actual element order is significant. 
	 * @return ArrayObject Returns the array on success.
	 * @throws Exception Sorting failed.
	 */
	public function rsort($sort_flags = \SORT_REGULAR, $mode = ArrayInterface::COMPARE_VALUE, $preserve_keys = false);

	/**
	 * This method sorts the array using a user-supplied comparison function. 
	 * If the array you wish to sort needs to be sorted by some non-trivial 
	 * criteria, you should use this function. 
	 * 
	 * @param callable $callback The comparison function must return an integer 
	 * 			less than, equal to, or greater than zero if the first argument 
	 * 			is considered to be respectively less than, equal to, or greater 
	 * 			than the second. 
	 * @param integer $mode Determines whether the comparison should be made 
	 * 			on the array values or the array keys using the 
	 * 			ArrayInterface::COMPARE_VALUE and ArrayInterface::COMPARE_KEY constants.
	 * @param boolean $preserve_keys If this parameter is set to TRUE the array 
	 * 			indices maintain their correlation with the array elements they 
	 * 			are associated with. This is used mainly when sorting associative 
	 * 			arrays where the actual element order is significant. 
	 * @return ArrayObject Returns the array on success.
	 * @throws Exception Sorting failed.
	 */
	public function usort(callable $callback, $mode = ArrayInterface::COMPARE_VALUE, $preserve_keys = false);

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

	/**
	 * Returns all the values from the array and indexes the array numerically. 
	 * 
	 * @return ArrayObject The array of the values. 
	 */
	public function values();

	/**
	 * Applies the user-defined callback function to each element of the array. 
	 * 
	 * Only the values of the array may potentially be changed; its structure 
	 * cannot be altered, i.e., the programmer cannot add, unset or reorder 
	 * elements. If the callback does not respect this requirement, the 
	 * behavior of this function is undefined, and unpredictable. 
	 * 
	 * @param callable $callback Typically, callback takes on two parameters. 
	 *          The array parameter’s value being the first, and the key/index second. 
	 * @param mixed $userdata If the optional userdata parameter is supplied, 
	 *          it will be passed as the third parameter to the callback. 
	 * @return ArrayObject Returns the array on success.
	 * @throws RuntimeException Execution failed.
	 */
	public function walk(callable $callback, $userdata = NULL);
}
