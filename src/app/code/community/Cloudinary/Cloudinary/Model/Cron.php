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
        $cloudinary = Mage::getModel('cloudinary_cloudinary/extension');

        if ($cloudinary->isEnabled() && $cloudinary->migrationHasBeenTriggerd()) {
            $this->uploadImages();
        }

        return $this ;
    }

    private function uploadImages()
    {
        $baseMediaPath = Mage::getModel('catalog/product_media_config')->getBaseMediaPath();

        $images = Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection')
            ->findUnsynchronisedImages();

        foreach ($images as $image) {
            $path = sprintf('%s%s', $baseMediaPath, $image->getValue());

            if (file_exists($path)) {
                $this->_imageManager->uploadImage($path);
            }
        }
    }
} 
