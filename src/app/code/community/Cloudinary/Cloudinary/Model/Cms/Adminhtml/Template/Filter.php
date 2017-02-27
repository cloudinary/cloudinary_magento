<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\ImageFactory;
use CloudinaryExtension\UrlGenerator;

class Cloudinary_Cloudinary_Model_Cms_Adminhtml_Template_Filter extends Mage_Cms_Model_Adminhtml_Template_Filter
{
    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var UrlGenerator
     */
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

    /**
     * @param array $construction
     * @return string
     */
    public function mediaDirective($construction)
    {
        if (ini_get('allow_url_fopen')) {
            $image = $this->imageFactory->build(
                $this->imagePath($construction),
                function() use($construction) { return parent::mediaDirective($construction); }
            );

            return $this->urlGenerator->generateFor($image);
        }

        return parent::mediaDirective($construction);
    }

    /**
     * @param array $construction
     * @return string
     */
    protected function imagePath(array $construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);

        if (!isset($params['url'])) {
            Mage::throwException('Undefined url parameter for media directive.');
        }

        return $params['url'];
    }
}
