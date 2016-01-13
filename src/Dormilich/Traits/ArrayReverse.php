<?php

namespace Dormilich\Traits;

trait ArrayReverse
{
    /**
     * Return an array with elements in reverse order.
     * 
     * @param boolean $preserve_keys If set to TRUE numeric keys are preserved. 
     *          Non-numeric keys are not affected by this setting and will 
     *          always be preserved. 
     * @return ArrayObject Returns the reversed array. 
     */
    public function reverse($preserve_keys = false)
    {
        $flag  = filter_var($preserve_keys, \FILTER_VALIDATE_BOOLEAN);
        $array = array_reverse($this->getArrayCopy(), $flag);

        return $this->create($array);
    }
}
