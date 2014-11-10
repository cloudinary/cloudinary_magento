<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    public function getUrl()
    {
        $cloudinary = new ImageManager(new CloudinaryImageProvider(), new Configuration());
        return $cloudinary->getUrlForImage($this->_newFile);
    }
}