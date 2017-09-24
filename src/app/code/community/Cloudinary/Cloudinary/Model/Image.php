<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use Cloudinary_Cloudinary_Model_Exception_BadFilePathException as BadFilePathException;


class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{
    public function upload(array $imageDetails)
    {
        $configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        $imageManager = CloudinaryImageProvider::fromConfiguration($configuration);

        $fullPath = $this->_imageFullPathFromImageDetails($imageDetails);
        $relativePath = $configuration->isFolderedMigration() ? $configuration->getMigratedPath($fullPath) : '';

        $imageManager->upload(Image::fromPath($fullPath, $relativePath));

        Mage::getModel('cloudinary_cloudinary/synchronisation')
            ->setValueId($imageDetails['value_id'])
            ->setValue($imageDetails['file'])
            ->tagAsSynchronized();
    }

    private function _imageFullPathFromImageDetails($imageDetails)
    {
        return  $this->_getMediaBasePath() . $this->_getImageDetailFromKey($imageDetails, 'file');
    }

    private function _getImageDetailFromKey(array $imageDetails, $key)
    {
        if (!array_key_exists($key, $imageDetails)) {
            throw new BadFilePathException("Invalid image data structure. Missing " . $key);
        }
        return $imageDetails[$key];
    }

    private function _getMediaBasePath()
    {
        return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
    }
}
