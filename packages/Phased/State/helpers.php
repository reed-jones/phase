<?php

if (!function_exists('array_merge_phase')) {
    /**
     * Recursively merge two or more assoc arrays
     *
     * @param array $merged base merged array
     * @param array[] $rest the rest of the assoc arrays to be merged in
     *
     * @return array
     */
    function array_merge_phase(array $merged, ...$rest)
    {
        // Check the base case
        if (empty($rest)) {
            return $merged;
        }

        foreach($rest as $array) {
            if (!is_array($array)) {
                $array = [ $array ];
            }

            foreach ($array as $key => $value) {
                if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = array_merge_phase($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }
}
