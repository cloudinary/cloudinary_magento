<?php
 
class Cloudinary_Cloudinary_Model_Resource_Migration extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/migration', 'media_gallery_id');
        $this->_isPkAutoIncrement = false;
    }

}