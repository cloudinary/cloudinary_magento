<?php

use CloudinaryExtension\Image;
use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cms_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function mediaDirective($construction)
    {
        if ($this->_isEnabled()) {
            $imageManager = $this->_buildImageManager();
            $directiveParams = $construction[2];
            $params = $this->_getIncludeParameters($directiveParams);

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
        return ImageManagerFactory::buildFromConfiguration(
            $this->_getConfigHelper()->buildConfiguration()
        );
    }

}