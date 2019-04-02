<?php

class Cloudinary_Cloudinary_Model_System_Config_HexColor extends Mage_Core_Model_Config_Data
{

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->setValue('#' . preg_replace('/#/', '', $this->getValue()));
        return parent::_beforeSave();
    }
}
