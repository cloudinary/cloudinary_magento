<?php

class Cloudinary_Cloudinary_Model_Logger extends Mage_Core_Model_Abstract implements \CloudinaryExtension\Migration\Logger
{
    const SIGNATURE_TEMPLATE = "%s::%s ";

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

    public function debugLog($message)
    {
        if (Mage::getIsDeveloperMode()){
            Mage::log($this->getSignature() . $message . "\n", 1, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class']);
        }
    }

    /**
     * Add extra information to a log entry: class and funcion name from which the log is called
     * @param $message
     * @return string
     */
    public static function getSignature()
    {
        $parentTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2];
        $logSignature = sprintf(self::SIGNATURE_TEMPLATE, $parentTrace['class'], $parentTrace['function']);
        return $logSignature;
    }

    /**
     * @return Cloudinary_Cloudinary_Model_Logger
     */
    public static function getInstance()
    {
        return Mage::getModel('cloudinary_cloudinary/logger');
    }
}
