<?php

use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cron extends Mage_Core_Model_Abstract
{
    private $_imageManager;

    public function __construct()
    {
        Mage::helper('cloudinary_cloudinary/autoloader')->register();

        $this->_imageManager = ImageManagerFactory::fromConfiguration(
            Mage::helper('cloudinary_cloudinary/configuration')
        );
    }

    public function migrateImages()
    {
        $baseMediaPath = Mage::getModel('catalog/product_media_config')->getBaseMediaPath();
        $localMedia = Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection');

        $images = $localMedia->findAllUnsynchronisedImages();

        foreach ($images as $image) {
            $path = sprintf('%s%s', $baseMediaPath, $image->getValue());

            if (file_exists($path)) {
                $this->_imageManager->uploadImage($path);
            }
        }

        return $this ;
    }
} 
