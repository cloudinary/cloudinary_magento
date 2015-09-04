<?php

use CloudinaryExtension\CloudinaryImageProvider;
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
            ->load(Cloudinary_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID);

        $batchUploader = new BatchUploader(
            CloudinaryImageProvider::fromConfiguration(Mage::helper('cloudinary_cloudinary/configuration')->buildConfiguration()),
            $migrationTask,
            Mage::getModel('cloudinary_cloudinary/logger'),
            null
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

        foreach ($batchUploader->getMigrationErrors() as $error) {
            Cloudinary_Cloudinary_Model_MigrationError::saveFromException($error);
        }

        return $this;
    }
}

/*

TODO find out why:

2015-09-04T08:51:25+00:00 ERR (3): Cloudinary migration: Error in sending request to server - failed creating formpost data trying to upload /vagrant/public/media/catalog/product/2/0/20141004_224709_96.jpg - /media/catalog/product/2/0/20141004_224709_96.jpg
2015-09-04T08:51:25+00:00 ERR (3): Cloudinary migration: Error in sending request to server - failed creating formpost data trying to upload /vagrant/public/media/catalog/product/4/_/4_10.jpg - /media/catalog/product/4/_/4_10.jpg
2015-09-04T08:51:26+00:00 ERR (3): Cloudinary migration: Error in sending request to server - failed creating formpost data trying to upload /vagrant/public/media/catalog/product/4/_/4_sep_2014_09_52_23_1.jpg - /media/catalog/product/4/_/4_sep_2014_09_52_23_1.jpg

*/
