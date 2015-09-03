<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function getUrl()
    {
        $config = $this->_getConfigHelper();
        $file = $this->_newFile;
        if ($this->_imageShouldComeFromCloudinary($file)) {
            $imageProvider = CloudinaryImageProvider::fromConfiguration($config->buildConfiguration());
            $result = (string)$imageProvider->transformImage(Cloudinary_Cloudinary_Helper_Image::newApiImage($file));
        } else {
            $result = parent::getUrl();
        }
        Cloudinary_Cloudinary_Model_Logger::getInstance()->debugLog($result);
        return $result;
    }
}
