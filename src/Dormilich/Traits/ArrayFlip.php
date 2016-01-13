<?php

namespace Dormilich\Traits;

trait ArrayFlip
{
    /**
     * Exchanges all keys with their associated values in the array.
     * 
     * If a value has several occurrences, the latest key will be used as its 
     * value, and all others will be lost. 
     * 
     * @return ArrayObject Returns the flipped array.
     * @throws RuntimeException Failed to flip the array.
     */
    public function flip()
    {
        set_error_handler([$this, 'errorHandler']);

        $array = array_flip($this->getArrayCopy());

        restore_error_handler();
        return $this->create($array);
    }
}
