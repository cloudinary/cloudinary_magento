<?php

use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Freeform;

class Cloudinary_Cloudinary_Model_Transformation extends Mage_Core_Model_Abstract
{
    const TRANSFORM_CACHE_WARM_KEY = 'cloudinary_transform_warm';

    /**
     * @var Cloudinary_Cloudinary_Model_Configuration
     */
    private $configuration;

    /**
     * @var Cloudinary_Cloudinary_Model_Cache
     */
    private $cache;

    protected function _construct()
    {
        $this->configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        $this->cache = Mage::getSingleton('cloudinary_cloudinary/cache');
        $this->_init('cloudinary_cloudinary/transformation');
    }

    /**
     * @param string $imageFile
     * @return Transformation
     */
    public function transformationForImage($imageFile, Mage_Catalog_Model_Product $product = null)
    {
        return $this->addFreeformTransformationForImage(
            $this->configuration->getDefaultTransformation(),
            $imageFile,
            $product
        );
    }

    /**
     * @param Transformation $transformation
     * @param string $imageFile
     * @param Mage_Catalog_Model_Product|null $product
     * @return Transformation
     */
    public function addFreeformTransformationForImage(Transformation $transformation, $imageFile, Mage_Catalog_Model_Product $product = null)
    {
        $transformationString = false;
        if ($product) {
            $cloudinaryData = json_decode((string)$product->getCloudinaryData(), true) ?: array();
            if (isset($cloudinaryData['transformation']) && isset($cloudinaryData['transformation'][hash('sha256', $imageFile)])) {
                $transformationString = $cloudinaryData['transformation'][hash('sha256', $imageFile)];
            } else {
                $updateProduct = true;
                $transformationString = $this->cache->loadCache(
                    $this->getTransformCacheKeyFromImageFile($imageFile),
                    function () use ($imageFile) {
                        $this->warmTransformationCache();

                        $this->load($imageFile);
                        if (($this->getImageName() === $imageFile) && $this->hasFreeTransformation()) {
                            return $this->getFreeTransformation();
                        }

                        return '';
                    }
                );
            }
        } else {
            /*$transformationString = $this->cache->loadCache(
                $this->getTransformCacheKeyFromImageFile($imageFile),
                function () use ($imageFile) {
                    $this->warmTransformationCache();

                    $this->load($imageFile);
                    if (($this->getImageName() === $imageFile) && $this->hasFreeTransformation()) {
                        return $this->getFreeTransformation();
                    }
                    return '';
                }
            );*/
        }

        if (isset($updateProduct)) {
            //$initialEnvironmentInfo = Mage::getSingleton('core/app_emulation')->startEnvironmentEmulation(Mage_Core_Model_App::ADMIN_STORE_ID);
            $cloudinaryData['transformation'][hash('sha256', $imageFile)] = $transformationString;
            $product->setCloudinaryData(json_encode($cloudinaryData));
            $product->getResource()->saveAttribute($product, 'cloudinary_data');
            //Mage::getSingleton('core/app_emulation')->stopEnvironmentEmulation($initialEnvironmentInfo);
        }

        if ($transformationString != false) {
            $transformation->withFreeform(Freeform::fromString($transformationString));
        }

        return $transformation;
    }

    /**
     * Loads the whole transformation collection into cache to alleviate page load problems
     */
    protected function warmTransformationCache()
    {
        if ($this->cache->isEnabled() && !$this->cache->loadCache(self::TRANSFORM_CACHE_WARM_KEY)) {
            foreach ($this->getCollection() as $transformation) {
                $this->cache->saveCache(
                    $this->getTransformCacheKeyFromImageFile($transformation->getImageName()),
                    $transformation->getFreeTransformation()
                );
            }

            $this->cache->saveCache(self::TRANSFORM_CACHE_WARM_KEY, '1');
        }
    }

    private function getTransformCacheKeyFromImageFile($imageFile)
    {
        return sprintf('cloudinary_transform_%s', hash('sha256', $imageFile));
    }

    /**
     * @return bool
     */
    private function hasFreeTransformation()
    {
        return $this->getFreeTransformation() != null;
    }
}
