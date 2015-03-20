<?php
// ArrayObject.php

namespace Dormilich\Core;

class ArrayObject extends \ArrayObject # implements ArrayInterface
{
	public function errorHandler($code, $msg, $file, $line)
	{
		throw new \ErrorException($msg, 0, $code, $file, $line);
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

    	} catch (\ErrorException $exc) {
    		throw new \RuntimeException($exc->getMessage(), $exc->getCode(), $exc);
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
}