<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Exception\MigrationError;
use CloudinaryExtension\Migration\BatchUploader;

class Cloudinary_Cloudinary_Model_Cron extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        Mage::helper('cloudinary_cloudinary/autoloader')->register();
    }

    public function migrateImages()
    {
        $migrationTask = Mage::getModel('cloudinary_cloudinary/migration')
            ->load(Cloudinary_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID)
            ->recordBatchProgress();

        $batchUploader = new BatchUploader(
            CloudinaryImageProvider::fromConfiguration(
                Mage::getModel('cloudinary_cloudinary/configuration')
            ),
            $migrationTask,
            Mage::getModel('cloudinary_cloudinary/logger'),
            null,
            function(\Exception $e) {
                if ($e instanceof MigrationError) {
                    Cloudinary_Cloudinary_Model_MigrationError::saveFromException($e);
                }
            }
        );

        $combinedMediaRepository = new Cloudinary_Cloudinary_Model_SynchronisedMediaUnifier(
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
