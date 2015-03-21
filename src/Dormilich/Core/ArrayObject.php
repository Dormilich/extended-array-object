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
	 * Returns an array with all keys from the array lowercased or uppercased. 
	 * Numbered indices are left as is. 
	 * 
	 * If the case parameter is invalid then its default value (CASE_LOWER) 
	 * will be used instead.
	 * 
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

		// does not return FALSE if an array is provided
		$array = array_change_key_case($this->getArrayCopy(), $flag);

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
			if ($callback instanceof \Closure) {
				$callback = $callback->bindTo($this);
			}
			$array = array_filter($this->getArrayCopy(), $callback);
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
			// e.g. Notice: aray to string conversion
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
	 *          in each array. Receives the element’s value and key as parameters.
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

			if ($callback instanceof \Closure) {
				$callback = $callback->bindTo($this);
			}
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

			if ($callback instanceof \Closure) {
				$callback = $callback->bindTo($this);
			}
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