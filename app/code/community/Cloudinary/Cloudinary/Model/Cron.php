<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Exception\MigrationError;
use CloudinaryExtension\Migration\BatchUploader;
use CloudinaryExtension\Migration\BatchDownloader;

class Cloudinary_Cloudinary_Model_Cron extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        Mage::helper('cloudinary_cloudinary/autoloader')->register();
    }

    public function uploadMigration()
    {
        $migrationTask = Mage::getModel('cloudinary_cloudinary/migration')
            ->loadType(Cloudinary_Cloudinary_Model_Migration::UPLOAD_MIGRATION_TYPE)
            ->recordBatchProgress();

        $batchUploader = new BatchUploader(
            CloudinaryImageProvider::fromConfiguration(
                Mage::getModel('cloudinary_cloudinary/configuration')
            ),
            $migrationTask,
            Mage::getModel('cloudinary_cloudinary/logger'),
            null,
            function (\Exception $e) {
                if ($e instanceof MigrationError) {
                    Cloudinary_Cloudinary_Model_MigrationError::saveFromException($e, Cloudinary_Cloudinary_Model_Migration::UPLOAD_MIGRATION_TYPE);
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

    public function downloadMigration()
    {
        Mage::helper('cloudinary_cloudinary/BatchDownloader')->downloadImages();

        return $this;
    }
}
