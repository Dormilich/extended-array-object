<?php

namespace Dormilich\Traits;

trait ArrayUnique
{
    /**
     * returns a new array without duplicate values. 
     * 
     * @param integer $sort_flags The optional second parameter sort_flags 
     *          may be used to modify the sorting behavior.
     * @return ArrayObject Returns the filtered array. 
     * @throws ErrorExceeption Forced string conversion of a non-scalar value.
     */
    public function unique($sort_flags = \SORT_STRING)
    {
        set_error_handler([$this, 'errorHandler']);

        $array = array_unique($this->getArrayCopy(), $sort_flags);

        restore_error_handler();
        return $this->create($array);
    }
}
