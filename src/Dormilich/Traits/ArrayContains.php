<?php

namespace Dormilich\Traits;

trait ArrayContains
{
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
}
