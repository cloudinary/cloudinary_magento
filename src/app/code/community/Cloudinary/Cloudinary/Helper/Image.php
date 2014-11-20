<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\ImageManager;

class Cloudinary_Cloudinary_Helper_Image extends Mage_Catalog_Helper_Image
{
    private $_imageManager;

    private $_width;

    private $_height;

    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        $config = Mage::helper('cloudinary_cloudinary/configuration');

        $this->_imageManager = new ImageManager(new CloudinaryImageProvider(
            $config->buildCredentials(),
            Cloud::fromName($config->getCloudName())
        ));

        parent::init($product, $attributeName, $imageFile);

        return $this;
    }

    public function resize($width, $height = null)
    {
        $this->_width = $width;
        $this->_height = $height ?: $width;

        return $this;
    }

    public function __toString()
    {
        $imageFile = $this->getImageFile() ?: $this->getProduct()->getImage();

        return ($imageFile && $imageFile !== 'no_selection') ?
            $this->_imageManager->getUrlForImage($imageFile, array(
                'width' => $this->_width,
                'height' => $this->_height,
                'crop' => 'pad')
            ) :
            Mage::getDesign()->getSkinUrl($this->getPlaceholder());
    }
}
