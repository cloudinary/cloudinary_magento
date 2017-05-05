<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\ImageFactory;

class Cloudinary_Cloudinary_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    /**
     * @var ImageFactory
     */
    private $_imageFactory;

    public function __construct()
    {
        $this->_imageFactory = new ImageFactory(
            Mage::getModel('cloudinary_cloudinary/configuration'),
            Mage::getModel('cloudinary_cloudinary/synchronizationChecker')
        );

        return parent::__construct();
    }

    public function getUrl()
    {
        return (string) $this->_imageFactory->build(
            $this->_newFile, function() { return parent::getUrl();}
        );
    }

    public function getKeepFrameState()
    {
        return $this->_keepFrame;
    }
}
