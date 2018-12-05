<?php

class Cloudinary_Cloudinary_Helper_Reset extends Mage_Core_Helper_Abstract
{
    const CONFIG_CACHE = 'config';

    public function removeModuleData()
    {
        $this->truncateCollection('cloudinary_cloudinary/synchronisation');
        $this->truncateCollection('cloudinary_cloudinary/migrationError');
        $this->truncateCollection('cloudinary_cloudinary/transformation');
        $this->removeConfigurationData();
        $this->removeMigration();
        $this->clearConfigCache();
    }

    /**
     * @param string $collectionName
     */
    private function truncateCollection($collectionName)
    {
        $collection = Mage::getModel($collectionName)->getCollection();
        return $collection->getConnection()->query(sprintf('TRUNCATE %s', $collection->getMainTable()));
    }

    private function removeConfigurationData()
    {
        $resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core_write');
        $table = $resource->getTableName('core/config_data');
        $write->delete($table, array('path LIKE (?)' => 'cloudinary/%'));
    }

    private function removeMigration()
    {
        Mage::getModel('cloudinary_cloudinary/migration')
            ->load(Cloudinary_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID)
            ->delete();
    }

    private function clearConfigCache()
    {
        Mage::app()->getCacheInstance()->cleanType(self::CONFIG_CACHE);
        Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => self::CONFIG_CACHE));
    }
}
