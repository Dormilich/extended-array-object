<?php

namespace Dormilich\Traits;

trait ArrayRand
{
    /**
     * Picks one or more random entries out of an array, and returns them.
     * Num must be a value between 1 and the array length.
     * 
     * @param integer $num Specifies how many entries should be picked. 
     * @return ArrayObject Returns an array of the selected entries.
     * @throws InvalidArgumentException Num argument is invalid or outside 
     *          the allowed range.
     */
    public function rand($num = 1)
    {
        $length = filter_var($num, \FILTER_VALIDATE_INT, ['options' => [
            'min_range' => 1, 
            'max_range' => $this->count(), 
        ]]);
        // extra test since TRUE would pass the int validation
        if (!$length or !is_numeric($num)) {
            throw new \InvalidArgumentException('Invalid length specifier given.');
        }
        $array = $this->getArrayCopy();
        $keys  = (array) array_rand($array, $length);
        $array = array_intersect_key($array, array_flip($keys));

        return $this->create($array);
    }
}
