<?php
/**
 * @author Shubin Sergei <is.captain.fail@gmail.com>
 * @license GNU General Public License v3.0
 * 08.03.2020 2020
 */
if (!function_exists('array_last')) {
    function array_last($array)
    {
        $array = (array)$array;
        return end($array);
    }
}
if (!function_exists('array_first')) {
    function array_first($array)
    {
        foreach ((array)$array as $item) {
            return $item;
        }
        return false;
    }
}
