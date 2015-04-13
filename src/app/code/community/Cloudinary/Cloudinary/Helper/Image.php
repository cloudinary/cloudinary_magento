<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation\Dimensions;

class Cloudinary_Cloudinary_Helper_Image extends Mage_Catalog_Helper_Image
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    private $_imageProvider;
    private $_dimensions;
    private $_attributeName;
    private $_configuration;

    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        if ($this->_isEnabled()) {

            $this->_configuration = $this->_getConfigHelper()->buildConfiguration();

            $this->_dimensions = Dimensions::null();
            $this->_attributeName = $attributeName;

            $this->_imageProvider = CloudinaryImageProvider::fromConfiguration(
                $this->_configuration
            );
        }

        return parent::init($product, $attributeName, $imageFile);
    }

    public function resize($width, $height = null)
    {
        if ($this->_imageShouldComeFromCloudinary($this->_getRequestedImageFile())) {
            $this->_dimensions = Dimensions::fromWidthAndHeight($width, $height ?: $width);
            return $this;
        }

        return parent::resize($width, $height);
    }

    private function _getRequestedImageFile()
    {
        return $this->getImageFile() ?: $this->getProduct()->getData($this->_attributeName);
    }

    public function __toString()
    {
        $imageFile = $this->_getRequestedImageFile();

        if ($this->_imageShouldComeFromCloudinary($imageFile)) {
            $image = Image::fromPath($imageFile);

            $transformation = $this->_configuration->getDefaultTransformation()
                ->withDimensions($this->_dimensions);

            return (string)$this->_imageProvider->transformImage($image, $transformation);
        }

        return parent::__toString();
    }
}
