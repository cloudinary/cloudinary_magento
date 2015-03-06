<?php

use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Dimensions;
use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cms_Wysiwyg_Images_Storage extends Mage_Cms_Model_Wysiwyg_Images_Storage
{

    public function getThumbnailUrl($filePath, $checkFile = false)
    {
        if ($this->_getConfigHelper()->isEnabled() && $this->_isImageInCloudinary($filePath)) {
            $imageManager = $this->_buildImageManager();
            $imageDimensions = $this->_buildImageDimensions();
            $defaultTransformation = $imageManager->getDefaultTransformation();

            return $imageManager->getUrlForImageWithTransformation(
                Image::fromPath($filePath),
                $defaultTransformation->withDimensions($imageDimensions)
            );
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

    private function _buildImageDimensions()
    {
        return Dimensions::fromWidthAndHeight(
            $this->getConfigData('resize_width'),
            $this->getConfigData('resize_height')
        );
    }

    private function _isImageInCloudinary($imageName)
    {
        return Mage::getModel('cloudinary_cloudinary/synchronisation')->isImageInCloudinary(basename($imageName));
    }

    public function uploadFile($targetPath, $type = null)
    {

        if(!$this->_getConfigHelper()->isEnabled()) {
           return parent::uploadFile($targetPath, $type);
        }

        $uploader = new Cloudinary_Cloudinary_Model_Cms_Uploader('image');
        if ($allowed = $this->getAllowedExtensions($type)) {
            $uploader->setAllowedExtensions($allowed);
        }
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $result = $uploader->save($targetPath);

        if (!$result) {
            Mage::throwException( Mage::helper('cms')->__('Cannot upload file.') );
        }

        // create thumbnail
        $this->resizeFile($targetPath . DS . $uploader->getUploadedFileName(), true);

        $result['cookie'] = array(
            'name'     => session_name(),
            'value'    => $this->getSession()->getSessionId(),
            'lifetime' => $this->getSession()->getCookieLifetime(),
            'path'     => $this->getSession()->getCookiePath(),
            'domain'   => $this->getSession()->getCookieDomain()
        );

        return $result;
    }

}