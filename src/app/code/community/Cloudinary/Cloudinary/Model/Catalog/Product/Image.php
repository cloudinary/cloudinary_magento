<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    public function getUrl()
    {
        $cloudinary = new ImageManager(new CloudinaryImageProvider(
            Mage::helper('cloudinary_cloudinary/configuration')->buildCredentials()
        ));

        return $cloudinary->getUrlForImage($this->_newFile);
    }
}