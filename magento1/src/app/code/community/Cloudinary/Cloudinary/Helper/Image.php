<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Configuration;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Dimensions;
use CloudinaryExtension\Image\Transformation\Crop;
use CloudinaryExtension\UrlGenerator;
use CloudinaryExtension\Image\ImageFactory;

class Cloudinary_Cloudinary_Helper_Image extends Mage_Catalog_Helper_Image
{
    /**
     * @var CloudinaryImageProvider
     */
    private $_imageProvider;

    /**
     * @var Dimensions
     */
    private $_dimensions;

    /**
     * @var string
     */
    private $_attributeName;

    /**
     * @var Configuration
     */
    private $_configuration;

    /**
     * @var Cloudinary_Cloudinary_Helper_ImageFactory
     */
    private $_imageFactory;

    /**
     * @var UrlGenerator
     */
    private $_urlGenerator;

    public function __construct()
    {
        $this->_configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        $this->_imageFactory = new ImageFactory(
            $this->_configuration,
            Mage::getModel('cloudinary_cloudinary/synchronizationChecker')
        );
        $this->_imageProvider = CloudinaryImageProvider::fromConfiguration($this->_configuration);
        $this->_dimensions = Dimensions::null();
        $this->_urlGenerator = new UrlGenerator($this->_configuration, $this->_imageProvider);
    }

    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        if ($this->_configuration->isEnabled()) {
            $this->_attributeName = $attributeName;
            $this->_dimensions = Dimensions::null();
        }

        return parent::init($product, $attributeName, $imageFile);
    }

    public function resize($width, $height = null)
    {
        $this->_dimensions = Dimensions::fromWidthAndHeight($width, $height);

        return parent::resize($width, $height);
    }

    public function getImageUrlForCategory(Mage_Catalog_Model_Category $category)
    {
        $imagePath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS . $category->getImage();

        $image = $this->_imageFactory->build($imagePath, array($category, 'getImageUrl'));

        return $this->_urlGenerator->generateFor($image);
    }

    public function __toString()
    {
        $image = $this->_imageFactory->build(
            $this->_getRequestedImageFile(),
            function() { return parent::__toString();}
        );

        return $this->_urlGenerator->generateFor($image, $this->createTransformation());
    }

    /**
     * @return string
     */
    private function _getRequestedImageFile()
    {
        return $this->getImageFile() ?: $this->getProduct()->getData($this->_attributeName);
    }

    private function createTransformation()
    {
        if ($this->_getModel()->getKeepFrameState()) {
            return $this->_configuration->getDefaultTransformation()
                ->withDimensions(Dimensions::squareMissingDimension($this->_dimensions))
                ->withCrop(Crop::pad());
        } else {
            return $this->_configuration->getDefaultTransformation()
                ->withDimensions($this->_dimensions)
                ->withCrop(Crop::fit());
        }
    }
}
