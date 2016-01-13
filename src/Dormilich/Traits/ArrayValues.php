<?php

namespace Dormilich\Traits;

trait ArrayValues
{
    /**
     * Returns all the values from the array and indexes the array numerically. 
     * 
     * @return ArrayObject The array of the values. 
     */
    public function values()
    {
        $values = array_values($this->getArrayCopy());

        return $this->create($values);
    }
}
