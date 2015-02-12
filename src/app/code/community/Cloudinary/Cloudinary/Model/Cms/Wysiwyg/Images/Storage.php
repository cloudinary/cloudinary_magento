<?php

use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Dimensions;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cms_Wysiwyg_Images_Storage extends Mage_Cms_Model_Wysiwyg_Images_Storage
{

    public function getThumbnailUrl($filePath, $checkFile = false)
    {
        if ($this->_getConfigHelper()->isEnabled() && $this->_isImageInCloudinary($filePath)) {
            $imageManager = $this->_buildImageManager();
            $image = Image::fromPath($filePath);
            return $imageManager->getUrlForImageWithTransformation($image, $this->_buildResizeTransformation());
        }
        return parent::getThumbnailUrl($filePath, $checkFile);
    }

    private function _getConfigHelper()
    {
        return Mage::helper('cloudinary_cloudinary/configuration');
    }

    private function _buildImageManager()
    {
        return ImageManagerFactory::buildFromConfiguration(
            $this->_getConfigHelper()->buildConfiguration()
        );
    }

    private function _buildResizeTransformation()
    {
        $dimensions = Dimensions::fromWidthAndHeight(
            $this->getConfigData('resize_width'),
            $this->getConfigData('resize_height')
        );
        return Transformation::toDimensions($dimensions);
    }

    private function _isImageInCloudinary($imageName)
    {
        return Mage::getModel('cloudinary_cloudinary/synchronisation')->isImageInCloudinary(basename($imageName));
    }

}