<?php

class Cloudinary_Cloudinary_Model_Resource_Media_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
    implements Cloudinary_Cloudinary_Model_Resource_Media_Collection_Interface
{

    protected function _construct()
    {
        $this->_init('catalog/product_attribute_backend_media');
    }
}