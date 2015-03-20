<?php
// ArrayObject.php

namespace Dormilich;

class ArrayObject extends \ArrayObject # implements ArrayInterface
{
	// using parent Constructor unmodified

	public function changeKeyCase($case = \CASE_LOWER)
	{
		$flag = filter_var($case, \FILTER_VALIDATE_INT, [
			'options' => [
				'min_range' => 0, 
				'max_range' => 1, 
				'default'   => \CASE_LOWER, 
			], 
		]);

		$array = array_change_key_case($this->getArrayCopy(), $flag);

		return new static($array);
	}
}