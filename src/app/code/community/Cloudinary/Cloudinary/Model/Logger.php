<?php

class Cloudinary_Cloudinary_Model_Logger extends Mage_Core_Model_Abstract implements \CloudinaryExtension\Migration\Logger
{
    public function warning($message, array $context = array())
    {
        Mage::log($message, Zend_Log::WARN);
    }

    public function notice($message, array $context = array())
    {
        Mage::log($message, Zend_Log::NOTICE);
    }

    public function error($message, array $context = array())
    {
        Mage::log($message, Zend_Log::ERR);
    }
}
