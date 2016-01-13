<?php

namespace Dormilich\Traits;

trait ArrayChangeKeyCase
{
    /**
     * Returns an array with all keys from the array lowercased or uppercased. 
     * Numbered indices are left as is. 
     * 
     * If the case parameter is invalid then its default value (CASE_LOWER) 
     * will be used instead.
     * 
     * @see http://php.net/manual/en/function.array-change-key-case.php#107715
     * @param integer $case Either CASE_UPPER, CASE_LOWER (default), or MB_CASE_TITLE.
     * @return ArrayObject Returns an array with its keys lower or uppercased.
     */
    public function changeKeyCase($case = \CASE_LOWER)
    {
        $flag = filter_var($case, \FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 0, 
                'max_range' => 1, 
                'default'   => \CASE_LOWER, 
            ], 
        ]);
        if ($case !== \MB_CASE_TITLE) {
            $case  = $flag === \CASE_LOWER ? \MB_CASE_LOWER : \MB_CASE_UPPER;
        }
        $array = [];

        foreach ($this as $key => $value) {
            $array[mb_convert_case($key, $case, 'UTF-8')] = $value;
        }

        return $this->create($array);
    }
}
