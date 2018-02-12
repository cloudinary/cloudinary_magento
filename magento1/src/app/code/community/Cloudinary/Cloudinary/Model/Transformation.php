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
    public function transformationForImage($imageFile)
    {
        return $this->addFreeformTransformationForImage(
            $this->configuration->getDefaultTransformation(),
            $imageFile
        );
    }

    /**
     * @param Transformation $transformation
     * @param string $imageFile
     * @return Transformation
     */
    public function addFreeformTransformationForImage(Transformation $transformation, $imageFile)
    {
        $transformationString = $this->cache->load(
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
        if ($this->cache->isEnabled() && !$this->cache->load(self::TRANSFORM_CACHE_WARM_KEY)) {
            foreach ($this->getCollection() as $transformation) {
                $this->cache->save(
                    $this->getTransformCacheKeyFromImageFile($transformation->getImageName()),
                    $transformation->getFreeTransformation()
                );
            }
            $this->cache->save(self::TRANSFORM_CACHE_WARM_KEY, '1');
        }
    }

    private function getTransformCacheKeyFromImageFile($imageFile)
    {
        return sprintf('cloudinary_transform_%s', md5($imageFile));
    }

    /**
     * @return bool
     */
    private function hasFreeTransformation()
    {
        return $this->getFreeTransformation() != null;
    }
}
