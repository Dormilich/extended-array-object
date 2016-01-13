<?php

namespace Dormilich\Traits;

trait ArrayJoin
{
    /**
     * Join the array’s elements with a string.
     * 
     * @param string $glue Defaults to an empty string. 
     * @return string Returns a string containing a string representation of 
     *          all the array elements in the same order, with the glue string 
     *          between each element. 
     * @throws ErrorExceeption Forced string conversion of a non-scalar value.
     */
    public function join($glue = '')
    {
        set_error_handler([$this, 'errorHandler']);

        $string = implode($glue, $this->getArrayCopy());

        restore_error_handler();
        return $string;
    }
}
