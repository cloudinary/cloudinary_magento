<?php

class Cloudinary_Cloudinary_Model_Resource_MigrationError extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/migrationError', 'file_path');
        $this->_isPkAutoIncrement = false;
    }
}
