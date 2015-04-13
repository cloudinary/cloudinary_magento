<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Dimensions;

class Cloudinary_Cloudinary_Model_Cms_Wysiwyg_Images_Storage extends Mage_Cms_Model_Wysiwyg_Images_Storage
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function getThumbnailUrl($filePath, $checkFile = false)
    {
        if ($this->_imageShouldComeFromCloudinary($filePath)) {
            $imageProvider = $this->_buildImageProvider();
            $imageDimensions = $this->_buildImageDimensions();
            $defaultTransformation = $this->_getConfigHelper()->buildConfiguration()->getDefaultTransformation();

            return (string)$imageProvider->transformImage(
                Image::fromPath($filePath),
                $defaultTransformation->withDimensions($imageDimensions)
            );
        }
        return parent::getThumbnailUrl($filePath, $checkFile);
    }

    private function _buildImageProvider()
    {
        return CloudinaryImageProvider::fromConfiguration($this->_getConfigHelper()->buildConfiguration());
    }

    private function _buildImageDimensions()
    {
        return Dimensions::fromWidthAndHeight(
            $this->getConfigData('resize_width'),
            $this->getConfigData('resize_height')
        );
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