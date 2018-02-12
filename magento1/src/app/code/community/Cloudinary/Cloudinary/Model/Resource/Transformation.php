<?php

class Cloudinary_Cloudinary_Model_Resource_Transformation extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/transformation', 'image_name');
    }
}
