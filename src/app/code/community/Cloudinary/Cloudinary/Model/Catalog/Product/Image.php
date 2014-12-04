<?php

use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function getUrl()
    {
        if ($this->_imageShouldComeFromCloudinary($this->_newFile)) {

            $imageManager = ImageManagerFactory::buildFromConfiguration(
                Mage::helper('cloudinary_cloudinary/configuration')->buildConfiguration()
            );

            return $imageManager->getUrlForImage(Image::fromPath($this->_newFile));
        }
        
        return parent::getUrl();
    }
}