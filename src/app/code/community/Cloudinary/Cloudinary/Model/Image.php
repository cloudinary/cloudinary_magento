<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;


class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    private $folder;

    public function upload(array $imageDetails)
    {
        if ($this->_getConfigHelper()->isFolderedMigration()) {
            $this->folder = $this->_getConfigHelper()->getMigratedPath($imageDetails['file']);
        }

        $imageManager = $this->_getImageProvider();
        $imageManager->upload(Image::fromPath($this->_imageFullPathFromImageDetails($imageDetails), $this->folder));

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
            throw new Cloudinary_Cloudinary_Model_Exception_BadFilePathException("Invalid image data structure. Missing " . $key);
        }
        return $imageDetails[$key];
    }

    private function _getMediaBasePath()
    {
        return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
    }

    public function deleteImage($imageName)
    {
        $this->_getImageProvider()->deleteImage(Cloudinary_Cloudinary_Helper_Image::newApiImage($imageName));
    }

    public function getUrl($imagePath)
    {
        $imageProvider = $this->_getImageProvider();

        return (string)$imageProvider->transformImage(Cloudinary_Cloudinary_Helper_Image::newApiImage($imagePath));
    }

    private function _getImageProvider()
    {
        return CloudinaryImageProvider::fromConfiguration($this->_getConfigHelper()->buildConfiguration());
    }
}
