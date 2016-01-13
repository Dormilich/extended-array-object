<?php

namespace Dormilich\Traits;

trait ArrayCountValues
{
    /**
     * Returns an array using the values of the array as keys and their 
     * frequency in the array as values. 
     * 
     * @return ArrayObject Returns an associative array of values from array 
     *          as keys and their count as value. 
     * @throws RuntimeException A value is not a string or integer.
     */
    public function countValues()
    {
        set_error_handler([$this, 'errorHandler']);

        $array = array_count_values($this->getArrayCopy());

        restore_error_handler();
        return $this->create($array);
    }
}
