<?php

class Cloudinary_Cloudinary_Model_Logger extends Mage_Core_Model_Abstract implements \CloudinaryExtension\Migration\Logger
{
    const SIGNATURE_TEMPLATE = "%s::%s ";
    const ALPHANUM_REGEX = '/[^A-Za-z0-9]/';
    const IGNORE_GLOBAL_LOG_FLAG = true;
    const MESSAGE_FORMAT = 'Cloudinary: %s';

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        $this->log($message, Zend_Log::WARN);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        $this->log($message, Zend_Log::NOTICE);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        $this->log($message, Zend_Log::ERR);
    }

    /**
     * @param string $message
     */
    public function debugLog($message)
    {
        if (Mage::getIsDeveloperMode()){
            Mage::log($this->getSignature() . $message . "\n", 1, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class']);
        }
    }

    /**
     * @param string $message
     * @param string $type
     */
    private function log($message, $type)
    {
        if ($this->isActive()) {
            Mage::log(
                sprintf(self::MESSAGE_FORMAT, $message),
                $type,
                $this->filename(),
                self::IGNORE_GLOBAL_LOG_FLAG
            );
        }
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return Mage::getModel('cloudinary_cloudinary/configuration')->hasLoggingActive();
    }

    /**
     * @return string|null
     */
    public function filename()
    {
        $filename = preg_replace(
            self::ALPHANUM_REGEX,
            '',
            Mage::getModel('cloudinary_cloudinary/configuration')->getLoggingFilename()
        );

        return $filename ? sprintf('%s.log', $filename) : null;
    }

    /**
     * Add extra information to a log entry: class and funcion name from which the log is called
     * @return string
     */
    public static function getSignature()
    {
        $parentTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2];
        $logSignature = sprintf(self::SIGNATURE_TEMPLATE, $parentTrace['class'], $parentTrace['function']);
        return $logSignature;
    }
}
