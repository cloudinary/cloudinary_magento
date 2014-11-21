<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Model_Catalog_Product_Media_Config extends Mage_Catalog_Model_Product_Media_Config
{
    public function getMediaUrl($file)
    {
        return $this->getUrlForImage($file);
    }

    public function getTmpMediaUrl($file)
    {
        return $this->getUrlForImage($file);
    }

    private function getUrlForImage($file)
    {
        $cloudinary = new ImageManager(new CloudinaryImageProvider(
            Mage::helper('cloudinary_cloudinary/configuration')->buildCredentials()
        ));

        return $cloudinary->getUrlForImage($file);
    }
} 