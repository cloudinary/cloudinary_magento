<?php

use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cron extends Mage_Core_Model_Abstract
{

    private $_imageManager;

    public function __construct()
    {
        Mage::helper('cloudinary_cloudinary/autoloader')->register();
        $this->_imageManager = ImageManagerFactory::buildFromConfiguration(
            Mage::helper('cloudinary_cloudinary/configuration')->buildConfiguration()
        );
    }

    public function migrateImages()
    {
        $migration = Mage::getModel('cloudinary_cloudinary/migration')->load(Cloudinary_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID);
        $syncMediaCollection = Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection');

        if ($migration->hasStarted()) {
            Mage::log('Cloudinary migration: processing');
            $images = $syncMediaCollection->findUnsynchronisedImages();

            if (!$images) {
                Mage::log('Cloudinary migration: complete');
                $migration->stop();
            } else {
                $this->_uploadImages($images);
            }
        }

        return $this ;
    }

    private function _uploadImages($images)
    {
        $countMigrated = 0;

        foreach ($images as $image) {

            try {
                $this->_synchronize($image);
                $countMigrated++;
                Mage::log(sprintf('Cloudinary migration: uploaded %s', $image->getValue()));
            } catch(Exception $e) {
                Mage::log(sprintf('Cloudinary migration: %s trying to upload %s', $e->getMessage(), $image->getValue()));
            }
        }

        Mage::log(sprintf('Cloudinary migration: %s images migrated', $countMigrated));
    }

    private function _synchronize($image)
    {
        $synchronization = Mage::getModel('cloudinary_cloudinary/synchronisation');
        $baseMediaPath = Mage::getModel('catalog/product_media_config')->getBaseMediaPath();

        $path = sprintf('%s%s', $baseMediaPath, $image->getValue());

        $this->_imageManager->uploadImage($path);

        $synchronization->tagImageAsBeingInCloudinary(array(
            'file' => basename($image->getValue()),
            'media_gallery_id' => $image->getValueId()
        ));
    }
}
