<?php

class Cloudinary_Cloudinary_Helper_Data extends Mage_Core_Helper_Abstract
{
    public static function curlGetContents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        return new Varien_Object(
            array(
            "code" => $httpCode,
            "body" => $result,
            "error" => $err
            )
        );
    }
}
