<?php

namespace Dormilich\Traits;

trait ArraySlice
{
    /**
     * Returns the sequence of elements from the array object as specified by 
     * the offset and length parameters. 
     * 
     * @param integer $offset If offset is non-negative, the sequence will 
     *          start at that offset in the array. If offset is negative, the 
     *          sequence will start that far from the end of the array. 
     * @param integer $length If length is given and is positive, then the 
     *          sequence will have up to that many elements in it. If the 
     *          array is shorter than the length, then only the available 
     *          array elements will be present. If length is given and is 
     *          negative then the sequence will stop that many elements from 
     *          the end of the array. If it is omitted, then the sequence will 
     *          have everything from offset up until the end of the array. 
     * @param boolean $preserve_keys Note that slice() will reorder and reset 
     *          the numeric array indices by default. You can change this 
     *          behaviour by setting preserve_keys to TRUE. 
     * @return ArrayObject Returns the slice. 
     */
    public function slice($offset, $length = NULL, $preserve_keys = false)
    {
        $array = array_slice($this->getArrayCopy(), $offset, $length, $preserve_keys);

        return $this->create($array);
    }
}
