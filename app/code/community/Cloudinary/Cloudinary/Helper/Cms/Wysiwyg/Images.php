<?php
/**
 * Wysiwyg Images Helper - Override for Mage_Cms_Helper_Wysiwyg_Images
 */
class Cloudinary_Cloudinary_Helper_Cms_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images
{

    /**
     * Images Storage root directory
     * Overrided in order to prevent realpath() when the media dir is a symlink
     *
     * @return string
     */
    public function getStorageRoot()
    {
        if (!Mage::getModel('cloudinary_cloudinary/configuration')->isEnabled()) {
            return parent::getStorageRoot();
        }
        if (!$this->_storageRoot) {
            $this->_storageRoot = Mage::getConfig()->getOptions()->getMediaDir()
                . DS . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY . DS;
        }
        return $this->_storageRoot;
    }

    /**
     * Return URL based on current selected directory or root directory for startup
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        if (!Mage::getModel('cloudinary_cloudinary/configuration')->isEnabled()) {
            return parent::getCurrentUrl();
        }
        if (!$this->_currentUrl) {
            $mediaPath = Mage::getConfig()->getOptions()->getMediaDir();
            $path = str_replace($mediaPath, '', $this->getCurrentPath());
            $path = trim($path, DS);
            $this->_currentUrl = Mage::app()->getStore($this->_storeId)->getBaseUrl('media') .
                                 $this->convertPathToUrl($path) . '/';
        }
        return $this->_currentUrl;
    }
}
