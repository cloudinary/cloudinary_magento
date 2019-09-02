<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Configuration;
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

    /**
     * @var Cloudinary_Cloudinary_Model_Transformation
     */
    private $_transformation;

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
        $this->_transformation = Mage::getModel('cloudinary_cloudinary/transformation');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $attributeName
     * @param string|null $imageFile
     * @return $this
     */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        if ($this->_configuration->isEnabled()) {
            $this->_attributeName = $attributeName;
            $this->_dimensions = Dimensions::null();
        }

        return parent::init($product, $attributeName, $imageFile);
    }

    /**
     * @param $width
     * @param null $height
     * @return $this
     */
    public function resize($width, $height = null)
    {
        if ($this->_configuration->isEnabled()) {
            $this->_dimensions = Dimensions::fromWidthAndHeight($width, $height);
        }

        return parent::resize($width, $height);
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getImageUrlForCategory(Mage_Catalog_Model_Category $category)
    {
        $imagePath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS . $category->getImage();

        $image = $this->_imageFactory->build($imagePath, array($category, 'getImageUrl'));

        return $this->_urlGenerator->generateFor($image);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (!$this->_configuration->isEnabled()) {
            return parent::__toString();
        }

        $image = $this->_imageFactory->build(
            $this->_getRequestedImageFile(),
            function () {
                return parent::__toString();
            }
        );

        return $this->_urlGenerator->generateFor(
            $image,
            $this->_transformation->addFreeformTransformationForImage(
                $this->createTransformation(),
                $this->_getRequestedImageFile(),
                $this->getProduct()
            )
        );
    }

    /**
     * @return string
     */
    private function _getRequestedImageFile()
    {
        return $this->getImageFile() ?: $this->getProduct()->getData($this->_attributeName);
    }

    /**
     * @return Transformation
     */
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
