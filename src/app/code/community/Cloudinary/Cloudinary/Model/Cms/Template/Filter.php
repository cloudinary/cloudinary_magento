<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Model_Cms_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function mediaDirective($construction)
    {
        if ($this->_isEnabled()) {
            $imageManager = $this->_buildImageManager();
            $params = $this->_getIncludeParameters($construction[2]);
            $imagePath = $params['url'];

            if ($this->_imageShouldComeFromCloudinary($imagePath)) {
                $image = Image::fromPath($imagePath);
                return $imageManager->getUrlForImage($image);
            }
        }
        return parent::mediaDirective($construction);
    }

    private function _buildImageManager()
    {
        return new ImageManager(new CloudinaryImageProvider(
            $this->_getConfigHelper()->buildCredentials(),
            Cloud::fromName($this->_getConfigHelper()->getCloudName())
        ));
    }

}