<?php

use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cron extends Mage_Core_Model_Abstract
{
    private $_imageManager;

    private $_cloudinaryConfig;

    public function __construct()
    {
        Mage::helper('cloudinary_cloudinary/autoloader')->register();
        $this->_cloudinaryConfig = Mage::helper('cloudinary_cloudinary/configuration');
        $this->_imageManager = ImageManagerFactory::fromConfiguration($this->_cloudinaryConfig);
    }

    public function migrateImages()
    {
        $cloudinary = Mage::getModel('cloudinary_cloudinary/extension')->load(1);
        $syncMediaCollection = Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection');

        if ($cloudinary->isEnabled() && $cloudinary->migrationHasBeenTriggered()) {

            $images = $syncMediaCollection->findUnsynchronisedImages();

            if (!$images) {
                Mage::log('Cloudinary migration: complete');
                $cloudinary->setMigrationTriggered(0);
                $cloudinary->save();
            } else {
                $this->uploadImages($images);
            }
        }

        return $this ;
    }

    private function uploadImages($images)
    {
        $baseMediaPath = Mage::getModel('catalog/product_media_config')->getBaseMediaPath();
        $countMigrated = 0;

        foreach ($images as $image) {
            $path = sprintf('%s%s', $baseMediaPath, $image->getValue());

            try {
                $this->_imageManager->uploadImage($path);
                $countMigrated++;
                Mage::log(sprintf('Cloudinary migration: uploaded %s', $image->getValue()));
            } catch(Exception $e) {
                Mage::log(sprintf('Cloudinary migration: %s for %s', $e->getMessage(), $image->getValue()));
            }

            $this->updateSyncronization($image);
        }

        Mage::log(sprintf('Cloudinary migration: %s images migrated', $countMigrated));
    }

    private function updateSyncronization($image)
    {
        $synchronization = Mage::getModel('cloudinary_cloudinary/synchronisation');
        $synchronization->setMediaGalleryId($image->getValueId());
        $synchronization->setImageName(basename($image->getValue()));
        $synchronization->save();
    }
} 
