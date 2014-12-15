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
        $migrationTask = Mage::getModel('cloudinary_cloudinary/migration')
            ->load(Cloudinary_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID);

        $batchUploader = new \CloudinaryExtension\Migration\BatchUploader(
            ImageManagerFactory::buildFromConfiguration(Mage::helper('cloudinary_cloudinary/configuration')->buildConfiguration()),
            $migrationTask,
            Mage::getModel('cloudinary_cloudinary/logger'),
            Mage::getModel('catalog/product_media_config')->getBaseMediaPath()
        );

        $migrationQueue = new \CloudinaryExtension\Migration\Queue(
            $migrationTask,
            Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection'),
            $batchUploader,
            Mage::getModel('cloudinary_cloudinary/logger')
        );

        $migrationQueue->process();

        return $this ;
    }
}
