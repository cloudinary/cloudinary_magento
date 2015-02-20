<?php

use CloudinaryExtension\ImageManagerFactory;
use CloudinaryExtension\Migration\SynchronizedMediaRepositoryRepository;

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
            null
        );

        $combinedMediaRepository = new SynchronizedMediaRepositoryRepository(
            array(
                Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection'),
                Mage::getResourceModel('cloudinary_cloudinary/cms_synchronisation_collection')
            )
        );

        $migrationQueue = new \CloudinaryExtension\Migration\Queue(
            $migrationTask,
            $combinedMediaRepository,
            $batchUploader,
            Mage::getModel('cloudinary_cloudinary/logger')
        );

        $migrationQueue->process();

        return $this;
    }
}
