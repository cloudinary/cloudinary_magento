<?php

use CloudinaryExtension\CredentialValidator;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;
use CloudinaryExtension\AutoUploadMapping\RequestProcessor;
use CloudinaryExtension\AutoUploadMapping\Configuration;
use CloudinaryExtension\AutoUploadMapping\ApiClient;

class Cloudinary_Cloudinary_Model_Observer_Config extends Mage_Core_Model_Abstract
{
    const CLOUDINARY_CONFIG_SECTION = 'cloudinary';
    const ERROR_WRONG_CREDENTIALS = 'There was a problem validating your Cloudinary credentials.';
    const ENABLED_FIELD = 'cloudinary/setup/fields/cloudinary_enabled';
    const ENVIRONMENT_FIELD = 'cloudinary/setup/fields/cloudinary_environment_variable';
    const CONFIG_CHANGE_MESSAGE = 'config saved: [%s]';
    const AUTO_UPLOAD_SETUP_FAIL_MESSAGE = 'error. Unable to setup auto upload mapping.';
    const AUTO_UPLOAD_SETUP_SUCCESS_MESSAGE = 'auto upload mapping configured: %s';

    /**
     * @param Varien_Event_Observer $observer
     */
    public function cloudinaryConfigChanged(Varien_Event_Observer $observer)
    {
        //Clear config cache before mapping
        Mage::app()->getCacheInstance()->cleanType("config");
        Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => "config"));
        Mage::getConfig()->reinit();

        if (!Mage::getModel('cloudinary_cloudinary/configuration')->isEnabled()) {
            return $this;
        }

        if (!$this->autoUploadRequestProcessor()->handle('media', Mage::getBaseUrl('media'), true)) {
            Mage::getSingleton('adminhtml/session')->addError(self::AUTO_UPLOAD_SETUP_FAIL_MESSAGE);
            Mage::getModel('cloudinary_cloudinary/logger')->error(self::AUTO_UPLOAD_SETUP_FAIL_MESSAGE);
        } else {
            $isActive = Mage::getModel('cloudinary_cloudinary/autoUploadMapping_configuration')->isActive();
            Mage::getModel('cloudinary_cloudinary/logger')->notice(
                sprintf(self::AUTO_UPLOAD_SETUP_SUCCESS_MESSAGE, $isActive ? 'On' : 'Off')
            );
        }
    }

    /**
     * @return RequestProcessor
     */
    private function autoUploadRequestProcessor()
    {
        return new RequestProcessor(
            Mage::getModel('cloudinary_cloudinary/autoUploadMapping_configuration'),
            ApiClient::fromConfiguration(Mage::getModel('cloudinary_cloudinary/configuration'))
        );
    }
}
