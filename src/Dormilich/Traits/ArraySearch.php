<?php

namespace Dormilich\Traits;

trait ArraySearch
{
    /**
     * Searches the array for a given value and returns the corresponding key 
     * if successful.
     * 
     * @param mixed $search_value The searched value. If search_value is a 
     *          string, the comparison is done in a case-sensitive manner. 
     * @param boolean $strict If the third parameter strict is set to TRUE 
     *          then the search() method will search for identical elements 
     *          in the array. This means it will also check the types of the 
     *          search_value in the array, and objects must be the same instance. 
     * @return mixed Returns the key for search_value if it is found in the 
     *          array, FALSE otherwise. 
     */
    public function search($search_value, $strict = false)
    {
        return array_search($search_value, $this->getArrayCopy(), $strict);
    }
}
