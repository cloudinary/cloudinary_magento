<?php
 
class Cloudinary_Cloudinary_Model_Resource_Extension extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/extension', 'cloudinary_extension_id');
    }

}