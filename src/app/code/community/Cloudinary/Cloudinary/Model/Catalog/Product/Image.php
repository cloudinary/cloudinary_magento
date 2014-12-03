<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function getUrl()
    {
        if($this->_imageShouldComeFromCloudinary($this->_newFile)) {
            $cloudinary = new ImageManager(new CloudinaryImageProvider(
                $this->_getConfigHelper()->buildCredentials(),
                Cloud::fromName($config->getCloudName())
            ));

            return $cloudinary->getUrlForImage(Image::fromPath($this->_newFile));
        }

        return parent::getUrl();
    }
}