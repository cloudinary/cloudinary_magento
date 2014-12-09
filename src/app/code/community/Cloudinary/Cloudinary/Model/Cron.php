<?php

use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cron extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        Mage::helper('cloudinary_cloudinary/autoloader')->register();
    }

    public function migrateImages()
    {
        $batchUploader = new \CloudinaryExtension\Migration\BatchUploader(
            ImageManagerFactory::buildFromConfiguration(Mage::helper('cloudinary_cloudinary/configuration')->buildConfiguration()),
            Mage::getModel('cloudinary_cloudinary/logger'),
            Mage::getModel('catalog/product_media_config')->getBaseMediaPath()
        );

        $migrationQueue = new \CloudinaryExtension\Migration\Queue(
            Mage::getModel('cloudinary_cloudinary/migration')->load(Cloudinary_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID),
            Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection'),
            $batchUploader,
            Mage::getModel('cloudinary_cloudinary/logger')
        );

        $migrationQueue->process();

        return $this ;
    }
}
