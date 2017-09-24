<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\ImageFactory;
use CloudinaryExtension\UrlGenerator;

class Cloudinary_Cloudinary_Model_Catalog_Product_Media_Config extends Mage_Catalog_Model_Product_Media_Config
{
    private $_configuration;
    private $_imageProvider;
    private $_urlGenerator;

    public function __construct()
    {
        $this->_configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        $this->_imageFactory = new ImageFactory(
            $this->_configuration,
            Mage::getModel('cloudinary_cloudinary/synchronizationChecker')
        );
        $this->_imageProvider = CloudinaryImageProvider::fromConfiguration($this->_configuration);
        $this->_urlGenerator = new UrlGenerator($this->_configuration, $this->_imageProvider);

    }

    public function getMediaUrl($file)
    {
        $image = $this->_imageFactory->build($file, function() use($file) { return parent::getMediaUrl($file);});

        return $this->_urlGenerator->generateFor($image);
    }

    public function getTmpMediaUrl($file)
    {
        $image = $this->_imageFactory->build($file, function() use($file) { return parent::getTmpMediaUrl($file);});

        return $this->_urlGenerator->generateFor($image);
    }
}
