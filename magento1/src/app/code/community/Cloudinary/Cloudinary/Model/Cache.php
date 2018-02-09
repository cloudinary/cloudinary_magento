<?php

class Cloudinary_Cloudinary_Model_Cache extends Mage_Core_Model_Abstract
{
    const CACHE_NAME = 'cloudinary';
    const CACHE_TAG = 'CLOUDINARY';

    private $mageCache;

    public function _construct()
    {
        $this->mageCache = Mage::app()->getCacheInstance();
    }

    public function isEnabled()
    {
        return $this->mageCache->canUse(self::CACHE_NAME);
    }

    public function save($key, $value)
    {
        $this->mageCache->save($value, $key, array(self::CACHE_TAG));
    }

    public function load($key, callable $uncachedCall = null)
    {
        $value = $this->isEnabled()
            ? $this->mageCache->load($key)
            : false;

        if ($value === false && !is_null($uncachedCall)) {
            $value = $uncachedCall();
            $this->save($key, $value);
        }

        return $value;
    }
}
