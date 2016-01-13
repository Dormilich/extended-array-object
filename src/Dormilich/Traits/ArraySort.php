<?php

namespace Dormilich\Traits;

trait ArraySort
{
    /**
     * Sorts the entries such that the keys maintain their correlation with 
     * the entries they are associated with. This is used mainly when sorting 
     * associative arrays where the actual element order is significant. 
     * 
     * @return ArrayObject Returns the array object.
     */
    public function asort()
    {
        parent::asort();

        return $this;
    }

    /**
     * Sorts the entries by key, maintaining key to entry correlations. 
     * This is useful mainly for associative arrays. 
     * 
     * @return ArrayObject Returns the array object.
     */
    public function ksort()
    {
        parent::ksort();

        return $this;
    }

    /**
     * This method implements a sort algorithm that orders alphanumeric 
     * strings in the way a human being would while maintaining key/value 
     * associations. This is described as a "natural ordering". 
     * 
     * This method is a case insensitive version of natsort().
     * 
     * @return ArrayObject Returns the array object.
     */
    public function natcasesort()
    {
        parent::natcasesort();

        return $this;
    }

    /**
     * This method implements a sort algorithm that orders alphanumeric 
     * strings in the way a human being would while maintaining key/value 
     * associations. This is described as a "natural ordering". 
     * 
     * @return ArrayObject Returns the array object.
     */
    public function natsort()
    {
        parent::natsort();

        return $this;
    }

    /**
     * This function sorts the entries such that keys maintain their 
     * correlation with the entry that they are associated with, using a 
     * user-defined comparison function.
     * 
     * This is used mainly when sorting associative arrays where the 
     * actual element order is significant. 
     * 
     * @param callable $cmp_function Function cmp_function should accept 
     *          two parameters which will be filled by pairs of entries. 
     *          The comparison function must return an integer less than, 
     *          equal to, or greater than zero if the first argument is 
     *          considered to be respectively less than, equal to, or 
     *          greater than the second. 
     * @return ArrayObject Returns the array object.
     * @throws RuntimeException Invalid callback definition given.
     */
    public function uasort($cmp_function)
    {
        set_error_handler([$this, 'errorHandler']);

        parent::uasort($cmp_function);

        restore_error_handler();
        return $this;
    }

    /**
     * This function sorts the keys of the entries using a user-supplied 
     * comparison function. The key to entry correlations will be maintained. 
     * 
     * @param callable $cmp_function Function cmp_function should accept 
     *          two parameters which will be filled by pairs of entry keys. 
     *          The comparison function must return an integer less than, 
     *          equal to, or greater than zero if the first argument is 
     *          considered to be respectively less than, equal to, or 
     *          greater than the second. 
     * @return ArrayObject Returns the array object.
     * @throws LogicException Invalid callback definition given.
     */
    public function uksort($cmp_function)
    {
        set_error_handler([$this, 'errorHandler']);

        parent::uksort($cmp_function);

        restore_error_handler();
        return $this;
    }
}
