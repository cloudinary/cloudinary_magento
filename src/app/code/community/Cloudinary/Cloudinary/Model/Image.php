a<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\ImageManager;
use CloudinaryExtension\ImageManagerFactory;
use CloudinaryExtension\Image;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;

class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function upload(array $imageDetails)
    {
        $imageManager = $this->_getImageManager();
        $imageManager->uploadImage($this->_imageFullPathFromImageDetails($imageDetails));

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
        $imageProvider = new CloudinaryImageProvider($this->_getCredentials(), $this->_getCloudName());
        $cloudinary = new ImageManager($imageProvider);
        $cloudinary->deleteImage(Image::fromPath($imageName));
    }

    private function _getCredentials()
    {
        $key = Key::fromString($this->_getConfigHelper()->getApiKey());
        $secret = Secret::fromString($this->_getConfigHelper()->getApiSecret());
        return new Credentials($key, $secret);
    }

    private function _getCloudName()
    {
        return Cloud::fromName($this->_getConfigHelper()->getCloudName());
    }

    public function getUrl($imagePath)
    {
        $imageManager = $this->_getImageManager();
        return $imageManager->getUrlForImage(Image::fromPath($imagePath));
    }

    private function _getImageManager()
    {
        return ImageManagerFactory::buildFromConfiguration(
            $this->_getConfigHelper()->buildConfiguration()
        );
    }
}
