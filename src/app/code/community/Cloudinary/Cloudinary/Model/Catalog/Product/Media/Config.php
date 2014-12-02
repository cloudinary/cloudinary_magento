<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManager;

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
        $config = Mage::helper('cloudinary_cloudinary/configuration');

        $cloudinary = new ImageManager(new CloudinaryImageProvider(
            $config->buildCredentials(),
            Cloud::fromName($config->getCloudName())
        ));

        return $cloudinary->getUrlForImage(Image::fromPath($file));
    }
} 