<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/10/2017
 * Time: 9:47 PM
 */


if (!function_exists('first_word')) {
    /**
     * Return the first word of a sentence/phrase.
     *
     * @param $str
     * @param string $delimiter
     * @return mixed
     */
    function first_word($str, $delimiter = ' ')
    {
        return array_first(explode($delimiter, $str));
    }
}

if (!function_exists('last_word')) {
    /**
     * Return the last word of a sentence/phrase.
     *
     * @param $str
     * @param string $delimiter
     * @return mixed
     */
    function last_word($str, $delimiter = ' ')
    {
        return array_last(explode($delimiter, $str));
    }
}

if (!function_exists('sanitise_non_utf8')) {
    /**
     * Remove non utf-8 characters from string.
     *
     * @param $str
     * @return mixed
     */
    function sanitise_non_utf8($str)
    {
        return preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $str);
    }
}

if (!function_exists('str_acronym')) {
    /**
     * Convert string to acronym
     *
     * @param $str
     * @return mixed
     */
    function str_acronym($str)
    {
        return preg_replace('~\b(\w)|.~', '$1', $str);
    }
}