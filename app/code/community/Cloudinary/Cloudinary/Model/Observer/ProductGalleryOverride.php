<?php

use CloudinaryExtension\ConfigurationInterface;

class Cloudinary_Cloudinary_Model_Observer_ProductGalleryOverride extends Mage_Core_Model_Abstract
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var Cloudinary_Cloudinary_Helper_ProductGalleryHelper
     */
    protected $productGalleryHelper;

    /**
     * @var Mage_Catalog_Block_Product_View_Media
     */
    protected $productGalleryBlock;

    protected $processed;
    protected $htmlId;

    /**
     * Cloudinary PG Options
     * @var array|null
     */
    protected $cloudinaryPGoptions;

    /**
     * @param Varien_Event_Observer $observer
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $this->configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        $this->productGalleryHelper = Mage::helper('cloudinary_cloudinary/ProductGalleryHelper');

        if (!$this->processed && $this->productGalleryHelper->canDisplayProductGallery()) {
            if (($productGalleryBlock = Mage::app()->getLayout()->getBlock('product.info.media'))) {
                $this->processed = true;
                $this->productGalleryBlock = $productGalleryBlock;
                $productGalleryBlock->setTemplate('cloudinary/catalog/product/view/media.phtml');
                $productGalleryBlock->setCloudinaryPGOptions($this->getCloudinaryPGOptions());
                $productGalleryBlock->setCldPGid($this->getCldPGid());
            }
        }
    }

    protected function getHtmlId()
    {
        if (!$this->htmlId) {
            $this->htmlId = md5(uniqid('', true));
        }
        return $this->htmlId;
    }

    protected function getCldPGid()
    {
        return 'cldPGid_' . $this->getHtmlId();
    }

    /**
     * @method getCloudinaryPGOptions
     * @param bool $refresh Refresh options
     * @param bool $ignoreDisabled Get te options even if the module or the product gallery are disabled
     * @return array
     */
    protected function getCloudinaryPGOptions($refresh = false, $ignoreDisabled = false)
    {
        if (is_null($this->cloudinaryPGoptions) || $refresh) {
            $this->cloudinaryPGoptions = $this->productGalleryHelper->getCloudinaryPGOptions($refresh, $ignoreDisabled);
            $this->cloudinaryPGoptions['container'] = '#' . $this->getCldPGid();
            $this->cloudinaryPGoptions['mediaAssets'] = [];
            if ($galleryAssets = $this->productGalleryBlock->getGalleryImages()) {
                foreach ($galleryAssets as $key => $_image) {
                    if ($this->productGalleryBlock->isGalleryImageVisible($_image)) {
                        $this->cloudinaryPGoptions['mediaAssets'][] = (object)[
                            "publicId" => $this->productGalleryBlock->getGalleryImageUrl($_image),
                            "mediaType" => 'image',
                        ];
                    }
                }
            }
        }
        return Mage::helper('core')->jsonEncode($this->cloudinaryPGoptions);
    }
}
