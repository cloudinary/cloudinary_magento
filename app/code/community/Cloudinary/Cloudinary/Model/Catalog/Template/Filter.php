<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use CloudinaryExtension\Image\ImageFactory;
use CloudinaryExtension\UrlGenerator;

/**
 * Catalog Template Filter Model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 * @todo        Needs to be reimplemented to get rid of the copypasted methods
 */
class Cloudinary_Cloudinary_Model_Catalog_Template_Filter extends Mage_Catalog_Model_Template_Filter
{
    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        if ($this->configuration->isEnabled()) {
            $this->imageFactory = new ImageFactory(
                $this->configuration,
                Mage::getModel('cloudinary_cloudinary/synchronizationChecker')
            );

            $this->urlGenerator = new UrlGenerator(
                $this->configuration,
                CloudinaryImageProvider::fromConfiguration($this->configuration)
            );
        }
    }

    /**
     * Retrieve media file URL directive
     *
     * @param array $construction
     * @return string
     * @see Mage_Core_Model_Email_Template_Filter::mediaDirective() method has been copypasted
     */
    public function mediaDirective($construction)
    {
        if (!$this->configuration->isEnabled()) {
            return parent::mediaDirective($construction);
        }

        $imagePath = $this->getImagePath($construction[2]);

        $image = $this->imageFactory->build(
            $imagePath,
            function () use ($construction) {
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
