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
}
