<?php

class Cloudinary_Cloudinary_Helper_Loggerutil
{
    /**
     * Small utility for improved logging. Prints class and function, and by default saves logs of a certain class to the file named after the class
     *
     * @param $message
     * @param int $level
     * @param null $fileName
     * @deprecated use Cloudinary_Cloudinary_Model_Logger::detail() instead
     */
    public static function log($message, $level = 7, $fileName = null)
    {
        $parentTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $class = $parentTrace['class'];
        $function = $parentTrace['function'];

        $logSignature = "$function: ";
        if ($fileName == null) {
            $fileName = $class;
        }
        if ($fileName != $class) {
            $logSignature = sprintf("%s::%s", $class, $logSignature);
        }

        Mage::log($logSignature . $message, $level, $fileName);
    }
}
