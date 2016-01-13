<?php

namespace Dormilich\Traits;

trait ArrayFilter
{
    /**
     * Iterates over each value in the array passing them to the callback 
     * function. If the callback function returns true, the current value from 
     * the array is returned into the result array. Array keys are preserved. 
     * 
     * @param callable $callback The callback function to use.
     * @return ArrayObject Returns the filtered array. 
     * @throws RuntimeException Invalid callback definition given.
     */
    public function filter(callable $callback)
    {
        set_error_handler([$this, 'errorHandler']);

        if (defined('ARRAY_FILTER_USE_BOTH')) {
            $array = array_filter($this->getArrayCopy(), $callback, \ARRAY_FILTER_USE_BOTH);
        }
        else {
            $array = [];
            foreach ($this as $key => $value) {
                if (call_user_func($callback, $value, $key)) {
                    $array[$key] = $value;
                }
            }
        }

        restore_error_handler();
        return $this->create($array);
    }
}
