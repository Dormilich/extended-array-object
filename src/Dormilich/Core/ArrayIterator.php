<?php
// ArrayIterator.php

namespace Dormilich\Core;

use Dormilich\Traits;

class ArrayIterator extends \ArrayIterator
{
    use Traits\ArrayChangeKeyCase,
        Traits\ArrayContains,
        Traits\ArrayCountValues,
        Traits\ArrayFilter,
        Traits\ArrayFlip,
        Traits\ArrayJoin,
        Traits\ArrayKeys,
        Traits\ArrayMap,
        Traits\ArrayRand,
        Traits\ArrayReverse,
        Traits\ArraySearch,
        Traits\ArrayShuffle,
        Traits\ArraySlice,
        Traits\ArraySort,
        Traits\ArrayUnique,
        Traits\ArrayValues,
        Traits\ErrorHandler
    ;

    /**
     * Create a new instance using the settings from the current instance.
     * 
     * @param mixed $input An array or object (with externally iterable properties)
     * @return static An new instance of the currently used class.
     */
    protected function create($input)
    {
        return new static($input, $this->getFlags());
    }

    /**
     * Push one or more elements onto the end of the array.
     * This method cannot be called when the ArrayIterator refers to an object. 
     * 
     * @param mixed $value The first value to push onto the end of the array. 
     * @return ArrayObject Returns the array.
     */
    public function push($value)
    {
        foreach (func_get_args() as $arg) {
            $this->append($arg);
        }
        return $this;
    }
}
