<?php

use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cron extends Mage_Core_Model_Abstract
{
    const CLOUDINARY_MIGRATION_ID = 1;

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
        $migration = Mage::getModel('cloudinary_cloudinary/migration')->load(self::CLOUDINARY_MIGRATION_ID);
        $syncMediaCollection = Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection');

        if ($this->_cloudinaryConfig->isEnabled() && $migration->hasStarted()) {
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
        $baseMediaPath = Mage::getModel('catalog/product_media_config')->getBaseMediaPath();
        $synchronization = Mage::getModel('cloudinary_cloudinary/synchronisation');
        $countMigrated = 0;

        foreach ($images as $image) {
            $path = sprintf('%s%s', $baseMediaPath, $image->getValue());

            try {
                $this->_imageManager->uploadImage($path);
                $countMigrated++;
                $synchronization->tagImageAsBeingInCloudinary(array(
                    'file' => basename($image->getValue()),
                    'media_gallery_id' => $image->getValueId()
                ));
                Mage::log(sprintf('Cloudinary migration: uploaded %s', $image->getValue()));
            } catch(Exception $e) {
                Mage::log(sprintf('Cloudinary migration: %s trying to upload %s', $e->getMessage(), $image->getValue()));
            }
        }

        Mage::log(sprintf('Cloudinary migration: %s images migrated', $countMigrated));
    }
}
