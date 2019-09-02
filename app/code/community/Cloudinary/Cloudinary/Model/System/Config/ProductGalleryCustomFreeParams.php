<?php

class Cloudinary_Cloudinary_Model_System_Config_ProductGalleryCustomFreeParams extends Mage_Core_Model_Config_Data
{
    const BAD_JSON_ERROR_MESSAGE = "Json error on 'Custom free parameters' please correct.";

    protected $_jsonErrors = array(
        JSON_ERROR_NONE => 'No error',
        JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'Syntax error',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    );

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $rawValue = $this->getValue();

        parent::_beforeSave();

        //Clear config cache
        Mage::app()->getCacheInstance()->cleanType("config");
        Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => "config"));
        Mage::getConfig()->reinit();

        if ($rawValue) {
            $data = @json_decode($rawValue);
            if ($data === null || $data === false) {
                $this->setValue('{}');
                try {
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Mage::getSingleton('adminhtml/session')->addError(self::BAD_JSON_ERROR_MESSAGE . ' (' . $this->_jsonErrors[json_last_error()] . ')');
                    }
                } catch (\Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError(self::BAD_JSON_ERROR_MESSAGE);
                }
            } else {
                $this->setValue(json_encode((object)$data));
            }
        } else {
            $this->setValue('{}');
        }
    }
}
