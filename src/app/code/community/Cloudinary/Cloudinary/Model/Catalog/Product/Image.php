<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    public function getUrl()
    {
        $imageManager = ImageManagerFactory::fromConfiguration(Mage::helper('cloudinary_cloudinary/configuration'));

        return $imageManager->getUrlForImage(Image::fromPath($this->_newFile));
    }
}