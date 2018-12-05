<?php

use CloudinaryExtension\Image\SynchronizationChecker as SynchronizationCheckerInterface;

class Cloudinary_Cloudinary_Model_SynchronizationChecker implements SynchronizationCheckerInterface
{
    /**
     * @param string $imageName
     * @return bool
     */
    public function isSynchronized($imageName)
    {
        if (!$imageName) {
            return false;
        }

        if ($this->hasAutoUploadMapping()) {
            return true;
        }

        /**
         * @var Cloudinary_Cloudinary_Model_Cache $cache
         */
        $cache = Mage::getSingleton('cloudinary_cloudinary/cache');

        if ($cache->isEnabled()) {
            return $this->cachedSynchronizationCheck($cache, $imageName);
        }

        return $this->synchronizationCheck($imageName);
    }

    /**
     * @return bool
     */
    private function hasAutoUploadMapping()
    {
        return Mage::getModel('cloudinary_cloudinary/autoUploadMapping_configuration')->isActive();
    }

    /**
     * @param $imageName
     * @return bool
     */
    private function synchronizationCheck($imageName)
    {
        $coll = Mage::getModel('cloudinary_cloudinary/synchronisation')->getCollection();
        $table = $coll->getMainTable();
        // case sensitive check
        $query = "select count(*) from $table where binary image_name = '$imageName' limit 1";
        return $coll->getConnection()->query($query)->fetchColumn() > 0;
    }

    /**
     * @param string $imageName
     * @return string
     */
    private function getSynchronizationCacheKeyFromImageName($imageName)
    {
        return sprintf('cloudinary_sync_%s', md5($imageName));
    }

    /**
     * @param Cloudinary_Cloudinary_Model_Cache $cache
     * @param string $imageName
     * @return bool
     */
    private function cachedSynchronizationCheck(Cloudinary_Cloudinary_Model_Cache $cache, $imageName)
    {
        return $cache->loadCache(
            $this->getSynchronizationCacheKeyFromImageName($imageName),
            function () use ($imageName) {
                return $this->synchronizationCheck($imageName) ? '1' : '0';
            }
        ) === '1' ? true : false;
    }
}
