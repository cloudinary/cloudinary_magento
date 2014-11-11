<?php
 
class Cloudinary_Cloudinary_Helper_Configuration extends Mage_Core_Helper_Abstract {

    public function getApiKey()
    {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig('cloudinary/credentials/cloudinary_api_key'));
    }

    public function getApiSecret()
    {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig('cloudinary/credentials/cloudinary_api_secret'));
    }

    public function getCloudName()
    {
        return Mage::getStoreConfig('cloudinary/cloud/cloudinary_cloud_name');
    }
}