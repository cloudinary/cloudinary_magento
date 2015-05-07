<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function getUrl()
    {
        if ($this->_imageShouldComeFromCloudinary($this->_newFile)) {

            $imageProvider = CloudinaryImageProvider::fromConfiguration($this->_getConfigHelper()->buildConfiguration());

            return (string)$imageProvider->transformImage(Image::fromPath($this->_newFile));
        }
        
        return parent::getUrl();
    }
}