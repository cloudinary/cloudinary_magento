<?php


class Cloudinary_Cloudinary_Helper_Util_ArrayUtils
{

    /**
     * Results with a subset of an associative array, preserving only the values that have a key that is present in $keys
     *
     * @param $array the original array we want to select from
     * @param $keys the keys to preserve in the input array
     * @return array
     */
    public static function arraySelect($array, $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $array[$key];
        }
        return $result;
    }
}

