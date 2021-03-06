<?php
// ArrayObject.php

namespace Dormilich\Core;

use Dormilich\Traits;

class ArrayObject extends \ArrayObject implements \JsonSerializable, ArrayInterface
{
	private $previous;

	use Traits\ArrayChangeKeyCase,
		Traits\ArrayContains,
		Traits\ArrayCountValues,
		Traits\ArrayFilter,
		Traits\ArrayFlip,
		Traits\ArrayJoin,
		Traits\ArrayKeys,
		Traits\ArrayMap,
		Traits\ArrayRand,
		Traits\ArrayReverse,
		Traits\ArraySearch,
		Traits\ArraySlice,
		Traits\ArraySort,
		Traits\ArrayUnique,
		Traits\ArrayValues,
		Traits\ErrorHandler
	;

	/**
	 * Create ArrayObject instance according to the native constructor 
	 * optionally adding the object from the previous action.
	 * 
	 * @param mixed $input An array or object (with externally iterable properties)
	 * @param integer $flags Flags to control the behaviour of the ArrayObject object.
	 * @param string $iterator_class Specify the class that will be used for iteration of the ArrayObject object.
	 * @param ArrayObject $previous ArrayObject of the operation that created this object.
	 * @return self
	 * @throws InvalidArgumentException $input is not an array or object
	 * @throws InvalidArgumentException $flags is not an integer
	 * @throws InvalidArgumentException $iterator_class is not an object that implements Iterator
	 */
	public function __construct($input = [], $flags = 0, $iterator_class = 'ArrayIterator', \ArrayObject $previous = null)
	{
		parent::__construct($input, $flags, $iterator_class);

		if ($previous) {
			$this->previous = $previous;
		}
	}

	/**
	 * Static constructor. The difference to the constructor is that it’s immediately available for chaining.
	 * 
	 * @param mixed $input An array or object (with externally iterable properties)
	 * @param integer $flags Flags to control the behaviour of the ArrayObject object.
	 * @param string $iterator_class Specify the class that will be used for iteration of the ArrayObject object.
	 * @param ArrayObject $previous ArrayObject of the operation that created this object.
	 * @return self
	 */
	public static function from($input, $flags = 0, $iterator_class = 'ArrayIterator')
	{
		return new static($input, $flags, $iterator_class);
	}

	/**
	 * Create a new instance using the settings from the current instance.
	 * 
	 * @param mixed $input An array or object (with externally iterable properties)
	 * @return static An new instance of the currently used class.
	 */
	protected function create($input)
	{
		return new static($input, $this->getFlags(), $this->getIteratorClass(), $this);
	}

	/**
	 * Get the previous ArrayObject if there is one. Otherwise throw an exception.
	 * 
	 * @return ArrayObject The previously used ArrayObject.
	 * @throws UnderflowException No object available.
	 */
	public function back()
	{
		if ($this->previous) {
			return $this->previous;
		}
		throw new \UnderflowException('There is no object available from the call history.');
	}

	/**
	 * Specify data which should be serialized to JSON. Serializes the object 
	 * to a value that can be serialized natively by json_encode().
	 * 
	 * @return mixed Returns data which can be serialized by json_encode(), 
	 *          which is a value of any type other than a resource. 
	 */
	public function jsonSerialize()
	{
		return $this->getArrayCopy();
	}

	/**
	 * Encodes the array data as JSON string.
	 * 
	 * @param integer $options A JSON_* encoding constant.
	 * @param integer $depth Set the maximum depth. Must be greater than zero. 
	 * @return string JSON encoded array data.
	 * @throws RuntimeException Conversion failed. Contains the JSON error code 
	 * 			(JSON_ERROR_*) from json_last_error().
	 */
	public function json()
	{
		$args   = func_get_args();
		array_unshift($args, $this);
		$result = call_user_func_array('json_encode', $args);

		if (false === $result) {
			throw new \RuntimeException('Failed to convert ArrayObject to JSON.', json_last_error());
		}
		return $result;
	}

	/**
	 * Convert all elements in the argument list into arrays and prepend 
	 * the array of this instance.
	 * 
	 * @param array $args The argument list.
	 * @return array The converted argument list.
	 * @throws RuntimeException Argument list is empty.
	 */
	protected function getArrayArgumentList(array $args)
	{
		if (count($args) === 0) {
			throw new \RuntimeException('Nothing to compare against given.');
		}

		$arg_list = array_map(function ($arg) {
			if ($arg instanceof \Iterator) {
				return iterator_to_array($arg);
			}
			return (array) $arg;
		}, $args);

		array_unshift($arg_list, $this->getArrayCopy());

		return $arg_list;
	}

	/**
	 * Convert all elements in the argument list into arrays. Integer values 
	 * are mapped to integer keys, arrays, ArrayObjects, and Iterators are 
	 * converted to arrays. Any other input element is converted to string 
	 * and turned into a string key.
	 * 
	 * @param array $args The argument list.
	 * @return array The converted argument list.
	 * @throws RuntimeException Argument list is empty.
	 */
	protected function getArrayKeyArgumentList(array $args)
	{
		if (count($args) === 0) {
			throw new \RuntimeException('Nothing to compare against given.');
		}

		// convert args into arrays or strings or integers
		$converted = array_map(function ($arg) {
			if (is_array($arg) or ($arg instanceof \ArrayObject)) {
				return (array) $arg;
			}
			if ($arg instanceof \Iterator) {
				return iterator_to_array($arg);
			}
			if (is_int($arg)) {
				return $arg;
			}
			return (string) $arg;
		}, $args);
		// extract the array arguments
		$arg_list = array_filter($converted, 'is_array');
		// prepend source array
		array_unshift($arg_list, $this->getArrayCopy());
		// extract the strings
		$strings  = array_filter($converted, 'is_string');
		// extract the integers
		$integers = array_filter($converted, 'is_int');
		// flip and merge strings/integers
		$combined = array_merge($strings, $integers);
		// flip and append 
		if (!empty($combined)) {
			$arg_list[] = array_flip($combined);
		}

		return $arg_list;
	}

	/**
	 * If the last argument is a callback, remove it from the argument array 
	 * and return it. If it is also a Closure bind the ArrayObject instance.
	 * 
	 * @param array $args A reference to the (method’s) arguments array.
	 * @return callable|false The callback or FALSE if not a callback.
	 */
	protected function getCallbackArgument(array &$args)
	{
		if (!is_callable(end($args))) {
			return false;
		}

		return array_pop($args);
	}

	/**
	 * If a valid flag is given, pop it off the argument’s list and return it.
	 * 
	 * @param array &$args A reference to the (method’s) arguments array.
	 * @return integer|false The flag, FALSE otherwise.
	 */
	protected function getFlagArgument(array &$args)
	{
		$flag = end($args);

		// need to prepend this check since switch() only does a loose check
		if (!is_int($flag)) {
			return false;
		}
		switch ($flag) {
			case ArrayInterface::COMPARE_KEY: 	// fall through
			case ArrayInterface::COMPARE_VALUE: // fall through
			case ArrayInterface::COMPARE_KEY|ArrayInterface::COMPARE_VALUE:
				return array_pop($args);
			default:
				return false;
		}
	}

	/**
	 * Executes an array_*_*assoc() function based on the compare callback 
	 * candidates and/or mode flag.
	 * 
	 * @param string $type Either diff or intersect.
	 * @param array $args The array arguments to pass to the chosen array_* 
	 *          function.
	 * @param mixed $value_compare A callable (user defined compare function) 
	 *          or NULL (internal compare function) or FALSE (not a compare 
	 *          function) to use to compare the values.
	 * @param mixed $key_compare A callable (user defined compare function) 
	 *          or NULL (internal compare function) or FALSE (not a compare 
	 *          function) to use to compare the keys. If only this is a valid 
	 *          callback it compares the keys or values depending on the value 
	 *          of the flag.
	 * @param mixed $flag Determines whether key_compare should be used on 
	 *          the keys or values.
	 * @return array Result of the array_*_*assoc() function.
	 */
	private function interdiffAssocExecute($type, array $args, $value_compare, $key_compare, $flag = null)
	{
		if ($value_compare and $key_compare) {
			$fn     = 'array_u%s_uassoc';
			$args[] = $value_compare;
			$args[] = $key_compare;
		}
		elseif (is_null($value_compare) and $key_compare) {
			$fn     = 'array_%s_uassoc';
			$args[] = $key_compare;
		}
		elseif ($value_compare and is_null($key_compare)) {
			$fn     = 'array_u%s_assoc';
			$args[] = $value_compare;
		}
		elseif (!$value_compare and $key_compare and $flag === ArrayInterface::COMPARE_VALUE) {
			$fn     = 'array_u%s_assoc';
			$args[] = $key_compare;
		}
		elseif (!$value_compare and $key_compare and $flag === ArrayInterface::COMPARE_KEY) {
			$fn     = 'array_%s_uassoc';
			$args[] = $key_compare;
		}
		elseif (!$value_compare and $key_compare and $flag === ArrayInterface::COMPARE_VALUE|ArrayInterface::COMPARE_KEY) {
			$fn     = 'array_u%s_uassoc';
			$args[] = $key_compare;
			$args[] = $key_compare;
		}
		else {
			$fn     = 'array_%s_assoc';
		}

		return call_user_func_array(sprintf($fn, $type), $args);
	}

	/**
	 * Prepare the array_*_*assoc() arguments from the method’s call parameters 
	 * and pass them to the executing method.
	 * 
	 * @param string $type Either diff or intersect
	 * @param array $args The method’s arguments array.
	 * @return array The result array of the function execution.
	 */
	private function interdiffAssocCall($type, array $args)
	{
		$flag = $this->getFlagArgument($args);

		if (!is_null(end($args))) {
			$callback1 = $this->getCallbackArgument($args);
		}
		else {
			$callback1 = array_pop($args);

		}

		if (!is_null(end($args))) {
			$callback2 = $this->getCallbackArgument($args);
		}
		else {
			$callback2 = array_pop($args);
		}

		$arg_list = $this->getArrayArgumentList($args);

		return $this->interdiffAssocExecute($type, $arg_list, $callback2, $callback1, $flag);
	}

	/**
	 * Merges the elements of the array with one or more arrays together so 
	 * that the values of one are appended to the end of the previous one. 
	 * For elements with the same string key, the later value overwrites the 
	 * previous one. Numeric keys are reindexed in the resulting array.
	 * 
	 * @param mixed $input First array to merge. 
	 * @return ArrayObject Returns the resulting array. 
	 */
	public function concat($input)
	{
		$args  = $this->getArrayArgumentList(func_get_args());
		$array = call_user_func_array('array_merge', $args);

		return $this->create($array);
	}

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
	 * @throws RuntimeException Missing comparison input.
	 * @throws RuntimeException Forced array conversion of a non-convertable 
	 * 			value.
	 */
	public function diff($input)
	{
		set_error_handler([$this, 'errorHandler']);

		$args     = func_get_args();
		$callback = $this->getCallbackArgument($args);
		$arg_list = $this->getArrayArgumentList($args);

		if ($callback) {
			$arg_list[] = $callback;
			$array = call_user_func_array('array_udiff', $arg_list);
		}
		else {
			$array = call_user_func_array('array_diff', $arg_list);
		}

		restore_error_handler();
		return $this->create($array);
	}

	/**
	 * Compares the input against the array object and returns the elements 
	 * in the input whose values are not present in the array object. This is 
	 * the reverse method to ArrayObject::diff().
	 * 
	 * @param mixed $input An array that is comared against the array object. 
	 * @param callable $callback (optional) A function that compares the 
	 * 			array values.
	 * @return ArrayObject The diff between input and array object.
	 * @throws RuntimeException Missing comparison input.
	 */
	public function xdiff($input, callable $callback = null)
	{
		if (is_null($callback) and is_callable($input)) {
			throw new \RuntimeException('Nothing to compare from given.');
		}
		$obj  = clone $this;
		$self = $obj->exchangeArray((array) $input);

		return $obj->diff($self, $callback);
	}

	/**
	 * Compares the array against one or more other arrays and returns the 
	 * elements in the array whose keys are not present in any of the other 
	 * arrays. 
	 * 
	 * If input is not an array, it will be converted to array keys. Multiple 
	 * scalar values are combined into a single input array before flipping.
	 * 
	 * @param mixed $input (multiple) First array to compare against.
	 * @param callable $callback (optional) A function that compares the 
	 * 			array keys.
	 * @return ArrayObject Returns an array containing all the entries from 
	 * 			the array that are not present in any of the other arrays. 
	 * @throws RuntimeException Missing comparison input.
	 * @throws RuntimeException Input cannot be converted to array keys.
	 */
	public function kdiff($input)
	{
		set_error_handler([$this, 'errorHandler']);

		$args     = func_get_args();
		$callback = $this->getCallbackArgument($args);
		$arg_list = $this->getArrayKeyArgumentList($args);

		if ($callback) {
			$arg_list[] = $callback;
			$array = call_user_func_array('array_diff_ukey', $arg_list);
		}
		else {
			$array = call_user_func_array('array_diff_key', $arg_list);
		}

		restore_error_handler();
		return $this->create($array);
	}

	/**
	 * Compares the input against the array object and returns the elements 
	 * in the input whose keys are not present in the array object. This is 
	 * the reverse method to ArrayObject::kdiff().
	 * 
	 * @param mixed $input An array that is comared against the array object. 
	 * @param callable $callback (optional) A function that compares the 
	 * 			array keys.
	 * @return ArrayObject The key diff between input and array object.
	 * @throws RuntimeException Missing comparison input.
	 */
	public function xkdiff($input, callable $callback = null)
	{
		if (is_null($callback) and is_callable($input)) {
			throw new \RuntimeException('Nothing to compare from given.');
		}
		$obj  = clone $this;
		$self = $obj->exchangeArray((array) $input);

		return $obj->kdiff($self, $callback);
	}

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
	 * @param integer $mode (conditional) If only one callback is given, this 
	 * 			flag determines whether it should be used for value or key 
	 * 			comparison. Can be either of ArrayInterface::COMPARE_VALUE or 
	 * 			ArrayInterface::COMPARE_KEY.
	 * @return ArrayObject Returns an array containing all the entries from 
	 * 			the array that are not present in any of the other arrays. 
	 * @throws RuntimeException Missing comparison input.
	 */
	public function adiff($input)
	{
		set_error_handler([$this, 'errorHandler']);

		$array = $this->interdiffAssocCall('diff', func_get_args());

		restore_error_handler();
		return $this->create($array);
	}

	/**
	 * Compares the input against the array object and returns the elements 
	 * in the input whose keys and values are not present in the array object. 
	 * This is the reverse method to ArrayObject::adiff().
	 * 
	 * @param mixed $input An array that is comared against the array object. 
	 * @param callable|null $value_compare_func (optional) Function to compare 
	 * 			the array values.
	 * @param callable|null $key_compare_func (optional) Function to compare 
	 * 			the array keys.
	 * @return ArrayObject The diff between input and array object.
	 * @throws RuntimeException Missing comparison input.
	 */
	public function xadiff($input)
	{
		$obj  = clone $this;
		$self = $obj->exchangeArray((array) $input);
		$num  = func_num_args();

		if ($num === 1) {
			return $obj->adiff($self);
		}
		elseif ($num === 3) {
			return $obj->adiff($self, func_get_arg(1), func_get_arg(2));
		}
		throw new \RuntimeException('Invalid number of arguments given. ' . __METHOD__ . ' requires exactly 1 or 3 arguments.');
	}

	/**
	 * Returns an array containing all the values of the array that are 
	 * present in all the arguments. Note that keys are preserved. 
	 * 
	 * If input is not an array it will be converted to an array. 
	 * 
	 * Note: There can only as many array elements in the result as the 
	 * shortest array’s length, even if the callback would allow more.
	 * 
	 * @param mixed $input (multiple) First array to compare against.
	 * @param callable $callback (optional) A function that compares the 
	 * 			array values.
	 * @return ArrayObject Returns an array containing all the entries from 
	 * 			the array that are present in all of the other arrays. 
	 * @throws RuntimeException Missing comparison input.
	 * @throws RuntimeException Forced array conversion of a non-convertable 
	 * 			value.
	 */
	public function intersect($input)
	{
		set_error_handler([$this, 'errorHandler']);

		$args     = func_get_args();
		$callback = $this->getCallbackArgument($args);
		$arg_list = $this->getArrayArgumentList($args);

		if ($callback) {
			$arg_list[] = $callback;
			$array = call_user_func_array('array_uintersect', $arg_list);
		}
		else {
			$array = call_user_func_array('array_intersect', $arg_list);
		}

		restore_error_handler();
		return $this->create($array);
	}

	/**
	 * Compares the input against the array object and returns the elements 
	 * in the input whose values are present in the array object. This is the 
	 * reverse method to ArrayObject::intersect().
	 * 
	 * @param mixed $input An array that is comared against the array object. 
	 * @param callable $callback (optional) A function that compares the 
	 * 			array values.
	 * @return ArrayObject The intersect between input and array object.
	 * @throws RuntimeException Missing comparison input.
	 */
	public function xintersect($input, callable $callback = null)
	{
		if (is_null($callback) and is_callable($input)) {
			throw new \RuntimeException('Nothing to compare from given.');
		}
		$obj  = clone $this;
		$self = $obj->exchangeArray((array) $input);

		if (is_null($callback)) {
			return $obj->intersect($self);
		}
		else {
			return $obj->intersect($self, $callback);
		}
	}

	/**
	 * Compares the array against one or more other arrays and returns the 
	 * elements in the array whose keys are present in all of the other 
	 * arrays. 
	 * 
	 * If input is not an array, it will be converted to array keys. Multiple 
	 * scalar values are combined into a single input array before flipping.
	 * 
	 * @param mixed $input (multiple) First array to compare against.
	 * @param callable $callback (optional) A function that compares the 
	 * 			array keys.
	 * @return ArrayObject Returns an array containing all the entries from 
	 * 			the array that are present in all of the other arrays. 
	 * @throws RuntimeException Missing comparison input.
	 * @throws RuntimeException Input cannot be converted to array keys.
	 */
	public function kintersect($input)
	{
		set_error_handler([$this, 'errorHandler']);

		$args     = func_get_args();
		$callback = $this->getCallbackArgument($args);
		$arg_list = $this->getArrayKeyArgumentList($args);

		if ($callback) {
			$arg_list[] = $callback;
			$array = call_user_func_array('array_intersect_ukey', $arg_list);
		}
		else {
			$array = call_user_func_array('array_intersect_key', $arg_list);
		}

		restore_error_handler();
		return $this->create($array);
	}

	/**
	 * Compares the input against the array object and returns the elements 
	 * in the input whose keys are present in the array object. This is the 
	 * reverse method to ArrayObject::kintersect().
	 * 
	 * @param mixed $input An array that is comared against the array object. 
	 * @param callable $callback (optional) A function that compares the 
	 * 			array keys.
	 * @return ArrayObject The key intersect between input and array object.
	 * @throws RuntimeException Missing comparison input.
	 */
	public function xkintersect($input, callable $callback = null)
	{
		if (is_null($callback) and is_callable($input)) {
			throw new \RuntimeException('Nothing to compare from given.');
		}
		$obj  = clone $this;
		$self = $obj->exchangeArray((array) $input);

		if (is_null($callback)) {
			return $obj->kintersect($self);
		}
		else {
			return $obj->kintersect($self, $callback);
		}
	}

	/**
	 * Compares the array against one or more other arrays and returns the 
	 * elements that are present in all of the other arrays using the keys 
	 * and values for comparison.
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
	 * @throws RuntimeException Input cannot be converted to an array.
	 * @throws RuntimeException Missing comparison input.
	 */
	public function aintersect($input)
	{
		set_error_handler([$this, 'errorHandler']);

		$array = $this->interdiffAssocCall('intersect', func_get_args());

		restore_error_handler();
		return $this->create($array);
	}

	/**
	 * Compares the input against the array object and returns the elements 
	 * in the input whose keys and values are present in the array object. 
	 * This is the reverse method to ArrayObject::aintersect().
	 * 
	 * @param mixed $input An array that is comared against the array object. 
	 * @param callable|null $value_compare_func (optional) Function to compare 
	 * 			the array values.
	 * @param callable|null $key_compare_func (optional) Function to compare 
	 * 			the array keys.
	 * @return ArrayObject The intersect between input and array object.
	 * @throws RuntimeException Missing comparison input.
	 */
	public function xaintersect($input)
	{
		$obj  = clone $this;
		$self = $obj->exchangeArray((array) $input);
		$num  = func_num_args();
		
		if ($num === 1) {
			return $obj->aintersect($self);
		}
		elseif ($num === 3) {
			return $obj->aintersect($self, func_get_arg(1), func_get_arg(2));
		}
		throw new \RuntimeException('Invalid number of arguments given. ' . __METHOD__ . ' requires exactly 1 or 3 arguments.');
	}

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
	public function merge($input)
	{
		set_error_handler([$this, 'errorHandler']);

		$args  = $this->getArrayArgumentList(func_get_args());
		$array = call_user_func_array('array_replace', $args);

		restore_error_handler();
		return $this->create($array);
	}

	/**
	 * Pops and returns the last value of the array, shortening the array by 
	 * one element. 
	 * 
	 * @return mixed Returns the last value of the array. If the array is 
	 * 			empty, NULL will be returned. 
	 */
	public function pop()
	{
		$array = $this->getArrayCopy();
		$value = array_pop($array);
		$this->exchangeArray($array);

		return $value;
	}

	/**
	 * Push one or more elements onto the end of the array.
	 * 
	 * @param mixed $value The first value to push onto the end of the array. 
	 * @return ArrayObject Returns the array.
	 */
	public function push($value)
	{
		foreach (func_get_args() as $arg) {
			$this->append($arg);
		}
		return $this;
	}

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
	 * @throws RuntimeException Invalid callback definition given.
	 */
	public function reduce(callable $callback)
	{
		if (func_num_args() === 1) {
			$carry = $this->shift();
		}
		else {
			$carry = func_get_arg(1);
		}

		set_error_handler([$this, 'errorHandler']);

		foreach ($this as $key => $value) {
			$carry = call_user_func($callback, $carry, $value, $key);
		}

		restore_error_handler();
		return $carry;
	}

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
	public function replace($input)
	{
		set_error_handler([$this, 'errorHandler']);

		$xargs    = new self(func_get_args());
		$arg_list = $xargs
			->map(function ($arg) {
				return $this->xkintersect($arg)->getArrayCopy();
			})
			->filter('count')
			->unshift($this->getArrayCopy())
			->getArrayCopy()
		;
		$array = call_user_func_array('array_replace', $arg_list);

		restore_error_handler();
		return $this->create($array);
	}

	/**
	 * Shifts the first value of the array off and returns it, shortening the 
	 * array by one element and moving everything down. All numerical array 
	 * keys will be modified to start counting from zero while literal keys 
	 * won’t be touched. 
	 * 
	 * @return mixed Returns the shifted value, or NULL if the array is empty.
	 */
	public function shift()
	{
		$array = $this->getArrayCopy();
		$value = array_shift($array);
		$this->exchangeArray($array);

		return $value;
	}

	/**
	 * Prepends passed elements to the front of the array. Note that the list 
	 * of elements is prepended as a whole, so that the prepended elements stay 
	 * in the same order. All numerical array keys will be modified to start 
	 * counting from zero while literal keys won’t be touched. 
	 * 
	 * @param mixed $value First value to prepend.  
	 * @return ArrayObject Returns the array.
	 */
	public function unshift($value)
	{
		$array = $this->getArrayCopy();
		$args  = array_merge([&$array], func_get_args());
		call_user_func_array('array_unshift', $args);

		$this->exchangeArray($array);

		return $this;
	}

	/**
	 * Shuffles (randomizes the order of the elements in) the array. 
	 * 
	 * @return ArrayObject Returns the array.
	 * @throws RuntimeException Shuffling failed.
	 */
	public function shuffle()
	{
		$array = $this->getArrayCopy();
		if (true !== shuffle($array)) {
			throw new \RuntimeException('Failed to shuffle the array.');
		}
		$this->exchangeArray($array);

		return $this;
	}

	/**
	 * Removes the elements designated by offset and length from the array 
	 * object, and replaces them with the elements of the replacement array, 
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
	 * @throws RuntimeException Too many arguments.
	 */
	public function splice($offset)
	{
		set_error_handler([$this, 'errorHandler']);

		$array = $this->getArrayCopy();
		$args  = array_merge([&$array], func_get_args());
		$slice = call_user_func_array('array_splice', $args);
		$this->exchangeArray($array);

		restore_error_handler();
		return $this->create($slice);
	}

	/**
	 * Applies the user-defined callback function to each element of the array. 
	 * 
	 * Only the values of the array may potentially be changed; its structure 
	 * cannot be altered, i.e., the programmer cannot add, unset or reorder 
	 * elements. If the callback does not respect this requirement, the 
	 * behavior of this function is undefined, and unpredictable. 
	 * 
	 * @param callable $callback Callback takes on three parameters. 
	 *          The element’s value, key, and the array itself. 
	 * @param mixed $userdata If the optional userdata parameter is supplied, 
	 *          it will be passed as the third parameter to the callback 
	 *          instead of the array. 
	 * @return ArrayObject Returns the array on success.
	 * @throws RuntimeException Invalid callback definition given.
	 * @throws RuntimeException Execution failed.
	 */
	public function walk(callable $callback, $userdata = NULL)
	{
		set_error_handler([$this, 'errorHandler']);

		$array = $this->getArrayCopy();

		if (NULL === $userdata) {
			$userdata = $array;
		}
		if (true !== array_walk($array, $callback, $userdata)) {
			restore_error_handler();
			throw new \RuntimeException('Execution of ' . __METHOD__ . ' failed.');
		}
		$this->exchangeArray($array);

		restore_error_handler();
		return $this;
	}
}
