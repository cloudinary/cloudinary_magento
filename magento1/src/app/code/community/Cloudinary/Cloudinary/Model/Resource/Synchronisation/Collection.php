<?php

use CloudinaryExtension\Migration\SynchronizedMediaRepository;

class Cloudinary_Cloudinary_Model_Resource_Synchronisation_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
    implements SynchronizedMediaRepository
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

    public function findUnsynchronisedImages($limit = 100)
    {
        $tableName = Mage::getSingleton('core/resource')->getTableName('cloudinary_cloudinary/catalog_media_gallery');
        $syncedImagesQuery = $this->getQueryForSyncedImageNames();

        $select = $this->getSelect();

        $select
            ->joinRight($tableName, 'value_id=media_gallery_id', '*')
            ->group('value')
            ->order('value')
            ->where("cloudinary_synchronisation_id is null and value not in ($syncedImagesQuery)")
            ->limit($limit);

        return $this->getItems();
    }

    /**
     * basically returns with all product image's media_gallery stored name which has been synced
     *
     * @return Varien_Db_Select
     */
    private function getQueryForSyncedImageNames()
    {
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->where('media_gallery_value is not null');
        return $select->columns('media_gallery_value');
    }

    /**
     * Only applicable for cms instance
     * @return [Cloudinary_Cloudinary_Model_Synchronisation]
     */
    public function findOrphanedSynchronisedImages()
    {
        return [];
    }
}
