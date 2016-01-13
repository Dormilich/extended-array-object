<?php

namespace Dormilich\Traits;

trait ArrayShuffle
{
    /**
     * Shuffles (randomizes the order of the elements in) the array. 
     * in contrast to shuffle() this does not modify the original array. 
     * 
     * @return ArrayObject Returns the shuffled array.
     * @throws RuntimeException Shuffling failed.
     */
    public function shuffle()
    {
        $array = $this->getArrayCopy();
        if (true !== shuffle($array)) {
            throw new \RuntimeException('Failed to shuffle the array.');
        }

        return $this->create($array);
    }
}
