<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    public function getUrl()
    {
        $config = Mage::helper('cloudinary_cloudinary/configuration');

        $cloudinary = new ImageManager(new CloudinaryImageProvider(
            $config->buildCredentials(),
            Cloud::fromName($config->getCloudName())
        ));

        return $cloudinary->getUrlForImage(Image::fromPath($this->_newFile));
    }
}