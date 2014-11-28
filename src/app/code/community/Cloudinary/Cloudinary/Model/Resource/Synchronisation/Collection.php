<?php
 
class Cloudinary_Cloudinary_Model_Resource_Synchronisation_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/synchronisation');
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

    public function findAllUnsynchronisedImages()
    {
        $this->getSelect()->where('in_cloudinary=1');

        return $this->join('catalog/product_attribute_media_gallery', 'value_id=media_gallery_id', '*');
    }
}