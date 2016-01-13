<?php

namespace Dormilich\Traits;

trait ArrayKeys
{
    /**
     * Return all the keys or a subset of the keys of the array.
     * 
     * @param mixed $search_value If specified, then only keys containing 
     *          these values are returned. 
     * @param boolean $strict Determines if strict comparison (===) should be 
     *          used during the search. 
     * @return ArrayObject Returns an array of all the (specified) keys in the array.  
     */
    public function keys()
    {
        $args = func_get_args();
        array_unshift($args, $this->getArrayCopy());
        $keys = call_user_func_array('array_keys', $args);

        return $this->create($keys);
    }
}
