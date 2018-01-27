<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 26/01/2018
 * Time: 11:14 PM
 */


if (!function_exists('ksort_recursive')) {
    /**
     * @param $array
     * @param int $sort_flags
     * @return bool
     */
    function ksort_recursive(&$array, $sort_flags = SORT_REGULAR) {
        if (!is_array($array)) return false;
        ksort($array, $sort_flags);
        foreach ($array as &$arr) {
            ksort_recursive($arr, $sort_flags);
        }
        return true;
    }
}