<?php

namespace Dormilich\Traits;

trait ArrayMap
{
    /**
     * Returns an array containing all the elements of the array after 
     * applying the callback function to each one.
     * 
     * @param callable $callback Callback function to run for each element 
     *          in the array. Receives the element’s value and key as parameters.
     * @param boolean $preserve_keys When set to TRUE keys will be preserved. 
     *          Default is FALSE which will reindex the array numerically.
     * @return ArrayObject Returns an array containing all the elements of the 
     *          array after applying the callback function to each one. 
     * @throws LogicException Invalid callback definition given.
     */
    public function map(callable $callback, $preserve_keys = false)
    {
        set_error_handler([$this, 'errorHandler']);

        $values = $this->getArrayCopy();
        $keys   = array_keys($values);
        $result = array_map($callback, $values, $keys);

        if ($preserve_keys) {
            $result = array_combine($keys, $result);
        }

        restore_error_handler();
        return $this->create($result);
    }
}
