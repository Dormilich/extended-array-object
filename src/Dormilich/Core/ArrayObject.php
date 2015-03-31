<?php
// ArrayObject.php

namespace Dormilich\Core;

class ArrayObject extends \ArrayObject implements \JsonSerializable #, ArrayInterface
{
	public function errorHandler($code, $msg, $file, $line)
	{
		throw new \ErrorException($msg, 0, $code, $file, $line);
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
	 * Returns an array with all keys from the array lowercased or uppercased. 
	 * Numbered indices are left as is. 
	 * 
	 * If the case parameter is invalid then its default value (CASE_LOWER) 
	 * will be used instead.
	 * 
	 * @see http://php.net/manual/en/function.array-change-key-case.php#107715
	 * @param integer $case Either CASE_UPPER or CASE_LOWER (default).
	 * @return ArrayObject Returns an array with its keys lower or uppercased.
	 */
	public function changeKeyCase($case = \CASE_LOWER)
	{
		$flag = filter_var($case, \FILTER_VALIDATE_INT, [
			'options' => [
				'min_range' => 0, 
				'max_range' => 1, 
				'default'   => \CASE_LOWER, 
			], 
		]);
		$case = $flag === \CASE_LOWER ? MB_CASE_LOWER : MB_CASE_UPPER;
		$array = [];

		foreach ($this as $key => $value) {
			$array[mb_convert_case($key, $case, 'UTF-8')] = $value;
		}

		return new static($array);
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
		$args  = array_map(function ($item) {
			return (array) $item;
		}, func_get_args());
		array_unshift($args, $this->getArrayCopy());
		$array = call_user_func_array('array_merge', $args);

		return new static($array);
	}

	/**
	 * Checks if a value exists in the array using loose comparison unless 
	 * strict is set.
	 * 
	 * @param mixed $needle The searched value.
	 * @param bool $strict If the parameter strict is set to TRUE then contains() 
	 *          will also check the types of the needle in the array.
	 * @return boolean Returns TRUE if needle is found in the array, FALSE otherwise. 
	 */
	public function contains($needle, $strict = false)
	{
		$flag  = filter_var($strict, \FILTER_VALIDATE_BOOLEAN);

		return in_array($needle, $this->getArrayCopy(), $flag);
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
		try {
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

			return new static($array);
		}
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \RuntimeException($exc->getMessage(), $exc->getCode(), $exc);
		}
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
		try {
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

			return new static($array);
		}
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \RuntimeException($exc->getMessage(), $exc->getCode(), $exc);
		}
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
	 * @throws RuntimeException Input cannot be converted to an array.
	 */
	public function adiff($input)
	{
		try {
			set_error_handler([$this, 'errorHandler']);
			$array = $this->interdiffAssocCall('diff', func_get_args());
			restore_error_handler();

			return new static($array);
		}
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \RuntimeException($exc->getMessage(), $exc->getCode(), $exc);
		}
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
	 */
	public function xadiff($input)
	{
		$obj  = clone $this;
		$self = $obj->exchangeArray((array) $input);
		
		$args = func_get_args();
		array_shift($args);
		$last = array_slice($args, -2);
		array_unshift($last, $self);

		return call_user_func_array([$obj, 'adiff'], $last);
	}

	/**
	 * Iterates over each value in the array passing them to the callback 
	 * function. If the callback function returns true, the current value from 
	 * the array is returned into the result array. Array keys are preserved. 
	 * 
	 * @param callable $callback The callback function to use.
	 * @return ArrayObject Returns the filtered array. 
	 * @throws LogicException Invalid callback definition given.
	 */
	public function filter(callable $callback)
	{
		try {
			set_error_handler([$this, 'errorHandler']);
			if (defined('ARRAY_FILTER_USE_BOTH')) {
				$array = array_filter($this->getArrayCopy(), $callback, \ARRAY_FILTER_USE_BOTH);
			}
			else {
				$array = array_filter($this->getArrayCopy(), $callback);
			}
			restore_error_handler();

			return new static($array);
		} 
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \LogicException($exc->getMessage(), $exc->getCode(), $exc);
		}
	}

	/**
	 * Exchanges all keys with their associated values in the array.
	 * 
	 * If a value has several occurrences, the latest key will be used as its 
	 * value, and all others will be lost. 
	 * 
	 * @return ArrayObject Returns the flipped array.
	 * @throws RuntimeException Failed to flip the array.
	 */
	public function flip()
	{
		try {
			set_error_handler([$this, 'errorHandler']);
			$array = array_flip($this->getArrayCopy());
			restore_error_handler();

			return new static($array);
		} 
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \RuntimeException($exc->getMessage(), $exc->getCode(), $exc);
		}
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
		try {
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

			return new static($array);
		}
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \RuntimeException($exc->getMessage(), $exc->getCode(), $exc);
		}
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
		try {
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

			return new static($array);
		}
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \RuntimeException($exc->getMessage(), $exc->getCode(), $exc);
		}
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
	 */
	public function aintersect($input)
	{
		try {
			set_error_handler([$this, 'errorHandler']);
			$array = $this->interdiffAssocCall('intersect', func_get_args());
			restore_error_handler();

			return new static($array);
		}
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \RuntimeException($exc->getMessage(), $exc->getCode(), $exc);
		}
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
	 */
	public function xaintersect($input)
	{
		$obj  = clone $this;
		$self = $obj->exchangeArray((array) $input);
		
		$args = func_get_args();
		array_shift($args);
		$last = array_slice($args, -2);
		array_unshift($last, $self);

		return call_user_func_array([$obj, 'aintersect'], $last);
	}

	/**
	 * Join the array’s elements with a string.
	 * 
	 * @param string $glue Defaults to an empty string. 
	 * @return string Returns a string containing a string representation of 
	 *          all the array elements in the same order, with the glue string 
	 *          between each element. 
	 * @throws RuntimeExceeption Forced string conversion of a non-scalar value.
	 */
	public function join($glue = '')
	{
		try {
			set_error_handler([$this, 'errorHandler']);
			$string = implode($glue, $this->getArrayCopy());
			restore_error_handler();

			return $string;
		}
		catch (\ErrorException $exc) {
			restore_error_handler();
			// e.g. Notice: array to string conversion
			throw new \RuntimeException($exc->getMessage(), $exc->getCode(), $exc);
		}
	}

	/**
	 * Return all the keys or a subset of the keys of the array.
	 * 
	 * @param mixed $search_value If specified, then only keys containing 
	 * 			these values are returned. 
	 * @param boolean $strict Determines if strict comparison (===) should be 
	 * 			used during the search. 
	 * @return ArrayObject Returns an array of all the (specified) keys in the array.  
	 */
	public function keys()
	{
		$args = func_get_args();
		array_unshift($args, $this->getArrayCopy());
		$keys = call_user_func_array('array_keys', $args);

		return new static($keys);
	}

	/**
	 * Returns an array containing all the elements of the array after 
	 * applying the callback function to each one.
	 * 
	 * @param callable $callback Callback function to run for each element 
	 *          in the array. Receives the element’s value and key as parameters.
	 * @param boolean $preserve_keys When set to TRUE keys will be preserved. 
	 * 			Default is FALSE which will reindex the array numerically.
	 * @return ArrayObject Returns an array containing all the elements of the 
	 *          array after applying the callback function to each one. 
	 * @throws LogicException Invalid callback definition given.
	 */
	public function map(callable $callback, $preserve_keys = false)
	{
		try {
			set_error_handler([$this, 'errorHandler']);

			$values = $this->getArrayCopy();
			$keys   = array_keys($values);
			$result = array_map($callback, $values, $keys);

			if ($preserve_keys) {
				$result = array_combine($keys, $result);
			}
			restore_error_handler();

			return new static($result);
		} 
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \LogicException($exc->getMessage(), $exc->getCode(), $exc);
		}
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
		try {
			set_error_handler([$this, 'errorHandler']);

			$args  = $this->getArrayArgumentList(func_get_args());
			$array = call_user_func_array('array_replace', $args);

			restore_error_handler();

			return new static($array);
		} 
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \LogicException($exc->getMessage(), $exc->getCode(), $exc);
		}
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
	 * Picks one or more random entries out of an array, and returns them.
	 * Num must be a value between 1 and the array length.
	 * 
	 * @param integer $num Specifies how many entries should be picked. 
	 * @return ArrayObject Returns an array of the selected entries.
	 * @throws InvalidArgumentException Num argument is invalid or outside 
	 * 			the allowed range.
	 */
	public function rand($num = 1)
	{
		$length = filter_var($num, \FILTER_VALIDATE_INT, ['options' => [
			'min_range' => 1, 
			'max_range' => $this->count(), 
		]]);
		// extra test since TRUE would pass the int validation
		if (!$length or !is_numeric($num)) {
			throw new \InvalidArgumentException('Invalid length specifier given.');
		}
		$array = $this->getArrayCopy();
		$keys  = (array) array_rand($array, $length);
		$array = array_intersect_key($array, array_flip($keys));

		return new static($array);
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
	 */
	public function reduce(callable $callback, $initial = NULL)
	{
		if ($this->count() === 0) {
			return $initial;
		}
		$array = $this->getArrayCopy();
		// assuming that NULL won’t be passed as initial value
		if (is_null($initial)) {
			$initial = array_shift($array);
		}

		return array_reduce($array, $callback, $initial);
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
		try {
			set_error_handler([$this, 'errorHandler']);
			$xargs = new self(func_get_args());
			$fn    = function ($arg) {
				return $this->xkintersect((array) $arg)->getArrayCopy();
			};
			$arg_list = $xargs
				->map($fn->bindTo($this))
				->filter('count')
				->unshift($this->getArrayCopy())
				->getArrayCopy()
			;
			$array = call_user_func_array('array_replace', $arg_list);

			restore_error_handler();

			return new static($array);
		} 
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \LogicException($exc->getMessage(), $exc->getCode(), $exc);
		}
	}

	/**
	 * Return an array with elements in reverse order.
	 * 
	 * @param boolean $preserve_keys If set to TRUE numeric keys are preserved. 
	 *          Non-numeric keys are not affected by this setting and will 
	 *          always be preserved. 
	 * @return ArrayObject Returns the reversed array. 
	 */
	public function reverse($preserve_keys = false)
	{
		$flag  = filter_var($preserve_keys, \FILTER_VALIDATE_BOOLEAN);
		$array = array_reverse($this->getArrayCopy(), $flag);

		return new static($array);
	}

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
	public function search($search_value, $strict = false)
	{
		return array_search($search_value, $this->getArrayCopy(), $strict);
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
		foreach (array_reverse(func_get_args()) as $arg) {
			// cannot use call_user_func_array() due to the reference
			array_unshift($array, $arg);
		}
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
	 * Sorts the entries such that the keys maintain their correlation with 
	 * the entries they are associated with. This is used mainly when sorting 
	 * associative arrays where the actual element order is significant. 
	 * 
	 * @return ArrayObject Returns the array object.
	 */
	public function asort()
	{
		parent::asort();

		return $this;
	}

	/**
	 * Sorts the entries by key, maintaining key to entry correlations. 
	 * This is useful mainly for associative arrays. 
	 * 
	 * @return ArrayObject Returns the array object.
	 */
	public function ksort()
	{
		parent::ksort();

		return $this;
	}

	/**
	 * This method implements a sort algorithm that orders alphanumeric 
	 * strings in the way a human being would while maintaining key/value 
	 * associations. This is described as a "natural ordering". 
	 * 
	 * This method is a case insensitive version of natsort().
	 * 
	 * @return ArrayObject Returns the array object.
	 */
	public function natcasesort()
	{
		parent::natcasesort();

		return $this;
	}

	/**
	 * This method implements a sort algorithm that orders alphanumeric 
	 * strings in the way a human being would while maintaining key/value 
	 * associations. This is described as a "natural ordering". 
	 * 
	 * @return ArrayObject Returns the array object.
	 */
	public function natsort()
	{
		parent::natsort();

		return $this;
	}

	/**
	 * This function sorts the entries such that keys maintain their 
	 * correlation with the entry that they are associated with, using a 
	 * user-defined comparison function.
	 * 
	 * This is used mainly when sorting associative arrays where the 
	 * actual element order is significant. 
	 * 
	 * @param callable $cmp_function Function cmp_function should accept 
	 *          two parameters which will be filled by pairs of entries. 
	 *          The comparison function must return an integer less than, 
	 *          equal to, or greater than zero if the first argument is 
	 *          considered to be respectively less than, equal to, or 
	 *          greater than the second. 
	 * @return ArrayObject Returns the array object.
	 */
	public function uasort(callable $cmp_function)
	{
		parent::uasort($cmp_function);

		return $this;
	}

	/**
	 * This function sorts the keys of the entries using a user-supplied 
	 * comparison function. The key to entry correlations will be maintained. 
	 * 
	 * @param callable $cmp_function Function cmp_function should accept 
	 *          two parameters which will be filled by pairs of entry keys. 
	 *          The comparison function must return an integer less than, 
	 *          equal to, or greater than zero if the first argument is 
	 *          considered to be respectively less than, equal to, or 
	 *          greater than the second. 
	 * @return ArrayObject Returns the array object.
	 */
	public function uksort(callable $cmp_function)
	{
		parent::uksort($cmp_function);

		return $this;
	}

	/**
	 * returns a new array without duplicate values. 
	 * 
	 * @param integer $sort_flags The optional second parameter sort_flags 
	 *          may be used to modify the sorting behavior.
	 * @return ArrayObject Returns the filtered array. 
	 * @throws RuntimeExceeption Forced string conversion of a non-scalar value.
	 */
	public function unique($sort_flags = \SORT_STRING)
	{
		try {
			set_error_handler([$this, 'errorHandler']);
			$array = array_unique($this->getArrayCopy(), $sort_flags);
			restore_error_handler();

			return new static($array);
		} 
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \RuntimeException($exc->getMessage(), $exc->getCode(), $exc);
		}
	}

	/**
	 * Returns all the values from the array and indexes the array numerically. 
	 * 
	 * @return ArrayObject The array of the values. 
	 */
	public function values()
	{
		$values = array_values($this->getArrayCopy());

		return new static($values);
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
	 * @throws LogicException Invalid callback definition given.
	 * @throws RuntimeException Execution failed.
	 */
	public function walk(callable $callback, $userdata = NULL)
	{
		try {
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
		catch (\ErrorException $exc) {
			restore_error_handler();
			throw new \LogicException($exc->getMessage(), $exc->getCode(), $exc);
		}
	}
}