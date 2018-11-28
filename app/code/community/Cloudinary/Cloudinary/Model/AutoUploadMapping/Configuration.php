<?php

use CloudinaryExtension\AutoUploadMapping\Configuration as ConfigurationInterface;

class Cloudinary_Cloudinary_Model_AutoUploadMapping_Configuration implements ConfigurationInterface
{
    const STATE_PATH = 'cloudinary/configuration/cloudinary_auto_upload_mapping_state';
    const REQUEST_PATH = 'cloudinary/configuration/cloudinary_auto_upload_mapping_request';

    /**
     * @return bool
     */
    public function isActive()
    {
        return Mage::getStoreConfigFlag(self::STATE_PATH);
    }

    /**
     * @param bool $state
     */
    public function setState($state)
    {
        $this->setStoreConfigFlag(self::STATE_PATH, $state);
    }

    /**
     * @return bool
     */
    public function getRequestState()
    {
        return Mage::getStoreConfigFlag(self::REQUEST_PATH);
    }

    /**
     * @param bool $state
     */
    public function setRequestState($state)
    {
        $this->setStoreConfigFlag(self::REQUEST_PATH, $state);
    }

    /**
     * @param string $configPath
     * @param bool $state
     */
    private function setStoreConfigFlag($configPath, $state)
    {
        $state = $state ? 1 : 0;
        $this->updatePersistentConfig($configPath, $state);
        $this->updateCacheConfig($configPath, $state);
    }

    /**
     * @param string $path
     * @param bool $state
     */
    private function updatePersistentConfig($path, $state)
    {
        Mage::getModel('core/config')->saveConfig($path, $state)->reinit();
    }

    /**
     * @param string $path
     * @param bool $state
     */
    private function updateCacheConfig($path, $state)
    {
        Mage::app()->getStore()->getConfig($path);
        Mage::app()->getStore()->setConfig($path, $state);
    }
}
