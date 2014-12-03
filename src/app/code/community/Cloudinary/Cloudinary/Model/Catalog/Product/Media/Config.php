<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManager;
use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Catalog_Product_Media_Config extends Mage_Catalog_Model_Product_Media_Config
{
    public function getMediaUrl($file)
    {
        return $this->_getUrlForImage($file);
    }

    public function getTmpMediaUrl($file)
    {
        return $this->_getUrlForImage($file);
    }

    private function _getUrlForImage($file)
    {
        $imageManager = ImageManagerFactory::buildFromConfiguration(
            Mage::helper('cloudinary_cloudinary/configuration')->buildConfiguration()
        );

        return $imageManager->getUrlForImage(Image::fromPath($file));
    }
} 