<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Dimensions;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Helper_Image extends Mage_Catalog_Helper_Image
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    private $_imageManager;
    private $_dimensions;
    private $_attributeName;

    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        if ($this->_isEnabled()) {
            $this->_dimensions = Dimensions::null();
            $this->_attributeName = $attributeName;

            $this->_imageManager = new ImageManager(
                new CloudinaryImageProvider(
                    $this->_getConfigHelper()->buildCredentials(),
                    Cloud::fromName($this->_getConfigHelper()->getCloudName())
                ),
                $this->_getConfigHelper()->buildConfiguration()->getDefaultTransformation()
            );
        }

        return parent::init($product, $attributeName, $imageFile);
    }

    public function resize($width, $height = null)
    {
        if($this->_imageShouldComeFromCloudinary($this->getRequestedImageFile())) {
            $this->_dimensions = Dimensions::fromWidthAndHeight($width, $height ?: $width);
            return $this;
        }

        return parent::resize($width, $height);
    }

    public function __toString()
    {
        $imageFile = $this->getRequestedImageFile();

        if($this->_imageShouldComeFromCloudinary($imageFile)) {
            $image = Image::fromPath($imageFile);

            $transformation = $this->_imageManager
                ->getDefaultTransformation()
                ->withDimensions($this->_dimensions);

            return $this->_imageManager->getUrlForImageWithTransformation($image, $transformation);
        }

        return parent::__toString();
    }

    private function getRequestedImageFile()
    {
        return $this->getImageFile() ?: $this->getProduct()->getData($this->_attributeName);
    }
}
