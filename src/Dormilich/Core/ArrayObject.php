<?php
// ArrayObject.php

namespace Dormilich\Core;

class ArrayObject extends \ArrayObject # implements ArrayInterface
{
	// using parent Constructor unmodified

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

	public function contains($needle, $strict = false)
	{
		$flag  = filter_var($strict, \FILTER_VALIDATE_BOOLEAN);

		return in_array($needle, $this->getArrayCopy(), $flag);
	}
}