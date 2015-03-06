<?php

class Cloudinary_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract
{

    const CLOUDINARY_CONFIG_SECTION = 'cloudinary';

    public function loadCustomAutoloaders(Varien_Event_Observer $event)
    {
        Mage::helper('cloudinary_cloudinary/autoloader')->register();

        return $event;
    }

    public function uploadImagesToCloudinary(Varien_Event_Observer $event)
    {
        if (Mage::helper('cloudinary_cloudinary/configuration')->isEnabled()) {
            $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');

            foreach ($this->_getImagesToUpload($event->getProduct()) as $image) {
                $cloudinaryImage->upload($image);
            }
        }
    }

    public function validateCloudinaryCredentials(Varien_Event_Observer $observer)
    {
        $configObject = $observer->getEvent()->getObject();
        if ($configObject->getSection() != self::CLOUDINARY_CONFIG_SECTION) {
            return;
        }

        $configData = $this->_flattenConfigData($configObject);

        $cloudinaryConfiguration = Mage::helper('cloudinary_cloudinary/configuration_validation');

        $cloudinaryConfiguration->validateCredentials(
            $configData['cloudinary_cloud_name'],
            $configData['cloudinary_api_key'],
            $configData['cloudinary_api_secret']
        );

    }

    private function _getImagesToUpload(Mage_Catalog_Model_Product $product)
    {
        return Mage::getModel('cloudinary_cloudinary/catalog_product_media')->newImagesForProduct($product);
    }

    public function deleteImagesFromCloudinary(Varien_Event_Observer $event)
    {
        $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');

        foreach ($this->_getImagesToDelete($event->getProduct()) as $image) {
            $cloudinaryImage->deleteImage($image['file']);
        }
    }

    private function _getImagesToDelete(Mage_Catalog_Model_Product $product)
    {
        $productMedia = Mage::getModel('cloudinary_cloudinary/catalog_product_media');
        return $productMedia->removedImagesForProduct($product);
    }

    private function _flattenConfigData(Mage_Adminhtml_Model_Config_Data $configObject)
    {
        $configData = array();
        $groups = $configObject->getGroups();
        foreach ($groups as $groupData) {
            foreach ($groupData['fields'] as $field => $fieldData) {
                $configData[$field] = (is_array($fieldData) && isset($fieldData['value']))
                    ? $fieldData['value'] : null;
            }
        }
        return $configData;
    }
}