<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image\ImageFactory;
use CloudinaryExtension\UrlGenerator;

class Cloudinary_Cloudinary_Model_Catalog_Product_Media_Config extends Mage_Catalog_Model_Product_Media_Config
{
    /**
     * @var ImageFactory
     */
    private $_imageFactory;

    /**
     * @var UrlGenerator
     */
    private $_urlGenerator;

    /**
     * @var Configuration
     */
    private $_configuration;

    public function __construct()
    {
        $this->_configuration = Mage::getModel('cloudinary_cloudinary/configuration');

        if ($this->_configuration->isEnabled()) {
            $this->_imageFactory = new ImageFactory(
                $this->_configuration,
                Mage::getModel('cloudinary_cloudinary/synchronizationChecker')
            );

            $this->_urlGenerator = new UrlGenerator(
                $this->_configuration,
                CloudinaryImageProvider::fromConfiguration($this->_configuration)
            );
        }
    }

    /**
     * @param string $file relative image filepath
     * @return string
     */
    public function getMediaUrl($file)
    {
        if (!$this->_configuration->isEnabled()) {
            return parent::getMediaUrl($file);
        }

        $image = $this->_imageFactory->build($file, function () use ($file) {
            return parent::getMediaUrl($file);
        });

        return $this->_urlGenerator->generateFor(
            $image,
            Mage::getModel('cloudinary_cloudinary/transformation')->transformationForImage($file)
        );
    }

    /**
     * @param string $file relative image filepath
     * @return string
     */
    public function getTmpMediaUrl($file)
    {
        //Comment this line & uncomment the next paragraph if you insist on using cloudinary for tmp media images
        return parent::getTmpMediaUrl($file);
        /*
        $file = DS . ltrim($this->getBaseTmpMediaUrlAddition(), DS) . $file;
        $image = $this->_imageFactory->build($file, function() use($file) { return parent::getTmpMediaUrl($file); });
        return $this->_urlGenerator->generateFor($image);
        */
    }
}
