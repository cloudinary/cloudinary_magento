<?php

use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cms_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function mediaDirective($construction)
    {
        if ($this->_isEnabled()) {
            $imagePath = $this->_getImagePath($construction[2]);

            if ($this->_imageShouldComeFromCloudinary($imagePath)) {
                $image = Image::fromPath($imagePath);
                return $this->_buildImageManager()->getUrlForImage($image);
            }
        }
        return parent::mediaDirective($construction);
    }

    private function _buildImageManager()
    {
        return ImageManagerFactory::buildFromConfiguration(
            $this->_getConfigHelper()->buildConfiguration()
        );
    }

    private function _getImagePath($directiveParams)
    {
        $params = $this->_getIncludeParameters($directiveParams);
        return $params['url'];
    }

}