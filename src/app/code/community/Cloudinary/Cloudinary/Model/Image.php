<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use Cloudinary_Cloudinary_Model_Exception_BadFilePathException as BadFilePathException;

class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{
    /**
     * @param array $imageDetails
     */
    public function upload(array $imageDetails)
    {
        $configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        $imageProvider = CloudinaryImageProvider::fromConfiguration($configuration);

        $fullPath = $this->_imageFullPathFromImageDetails($imageDetails);
        $relativePath = $configuration->isFolderedMigration() ? $configuration->getMigratedPath($fullPath) : '';

        $imageProvider->upload(Image::fromPath($fullPath, $relativePath));

        Mage::getModel('cloudinary_cloudinary/synchronisation')
            ->setValueId($imageDetails['value_id'])
            ->setValue($imageDetails['file'])
            ->tagAsSynchronized();
    }

    /**
     * @param array $imageDetails
     * @return string
     * @throws Cloudinary_Cloudinary_Model_Exception_BadFilePathException
     */
    private function _imageFullPathFromImageDetails(array $imageDetails)
    {
        return  $this->_getMediaBasePath() . $this->_getImageDetailFromKey($imageDetails, 'file');
    }

    /**
     * @param array $imageDetails
     * @param string $key
     * @return string
     * @throws Cloudinary_Cloudinary_Model_Exception_BadFilePathException
     */
    private function _getImageDetailFromKey(array $imageDetails, $key)
    {
        if (!array_key_exists($key, $imageDetails)) {
            throw new BadFilePathException("Invalid image data structure. Missing " . $key);
        }
        return $imageDetails[$key];
    }

    /**
     * @return string
     */
    private function _getMediaBasePath()
    {
        return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
    }

    /**
     * @param string $imageName
     */
    public function deleteImage($imageName)
    {
        $configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        $imageProvider = CloudinaryImageProvider::fromConfiguration($configuration);
        $migratedPath = $configuration->isFolderedMigration() ? $configuration->getMigratedPath($imageName) : '';
        $imageProvider->deleteImage(Image::fromPath($imageName, ltrim($migratedPath, '/')));
    }

    /**
     * @param string $imagePath
     * @return string
     */
    public function getUrl($imagePath)
    {
        $imageProvider = CloudinaryImageProvider::fromConfiguration(
            Mage::getModel('cloudinary_cloudinary/configuration')
        );

        return (string)$imageProvider->transformImage(Cloudinary_Cloudinary_Helper_Image::newApiImage($imagePath));
    }
}
