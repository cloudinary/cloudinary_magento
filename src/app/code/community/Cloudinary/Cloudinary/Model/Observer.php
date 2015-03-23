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
        if ($this->_isNotCloudinaryConfigurationSection($configObject)) {
            return;
        }

        try {
            $this->_validateEnvironmentVariableFromConfigObject($configObject);
        } catch (Exception $e) {
            $this->_addErrorMessageToAdminSession($e);
            $this->_logException($e);
        }
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

        if ($this->_containsSetup($groups)) {
            $configData = array_map(
                function($field) {
                    return $field['value'];
                },
                $groups['setup']['fields']
            );
        }
        return $configData;
    }

    private function _isNotCloudinaryConfigurationSection(Mage_Adminhtml_Model_Config_Data $configObject)
    {
        return $configObject->getSection() != self::CLOUDINARY_CONFIG_SECTION;
    }

    private function _validateEnvironmentVariableFromConfigObject(Mage_Adminhtml_Model_Config_Data $configObject)
    {
        $configData = $this->_flattenConfigData($configObject);
        $cloudinaryConfiguration = Mage::helper('cloudinary_cloudinary/configuration_validation');

        $cloudinaryConfiguration->validateEnvironmentVariable(
            $configData['cloudinary_environment_variable']
        );
    }

    private function _addErrorMessageToAdminSession($e)
    {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    }

    private function _logException($e)
    {
        Mage::logException($e);
    }

    private function _containsSetup($groups)
    {
        return array_key_exists('setup', $groups);
    }
}