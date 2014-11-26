<?php
 
class Cloudinary_Cloudinary_Model_Resource_Migration_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/migration');
    }

    private function _getResource()
    {
        return parent::getResource();
    }

    protected function _getConnection()
    {
        $resource = $this->_getResource();

        return $resource->getReadConnection();
    }

    protected function _getMainTable()
    {
        $resource = $this->_getResource();

        return $resource->getMainTable();
    }
}