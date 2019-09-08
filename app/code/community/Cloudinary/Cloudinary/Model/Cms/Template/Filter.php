<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\ImageFactory;
use CloudinaryExtension\UrlGenerator;

class Cloudinary_Cloudinary_Model_Cms_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    private $imageFactory;
    private $urlGenerator;

    public function __construct()
    {
        $configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        $this->imageFactory = new ImageFactory(
            $configuration,
            Mage::getModel('cloudinary_cloudinary/synchronizationChecker')
        );

        $this->urlGenerator = new UrlGenerator(
            $configuration,
            CloudinaryImageProvider::fromConfiguration($configuration)
        );

        parent::__construct();
    }

    public function mediaDirective($construction)
    {
        $imagePath = $this->getImagePath($construction[2]);
        
        $image = $this->imageFactory->build(
            $imagePath,
            function() use($construction) {
            return parent::mediaDirective($construction);
            }
        );

        return $this->urlGenerator->generateFor($image);
    }

    private function getImagePath($directiveParams)
    {
        $params = $this->_getIncludeParameters($directiveParams);
        return $params['url'];
    }
}
