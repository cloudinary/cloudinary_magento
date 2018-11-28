<?php

use CloudinaryExtension\CredentialValidator;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;
use CloudinaryExtension\AutoUploadMapping\RequestProcessor;
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
    public function configSave(Varien_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getObject();
        if ($config->getSection() != self::CLOUDINARY_CONFIG_SECTION) {
            return;
        }

        $data = Mage::helper('cloudinary_cloudinary/config')->flatten('cloudinary', $config->getGroups());
        if ($data[self::ENABLED_FIELD] == '1') {
            $this->validateEnvironmentVariable($data);
            $this->logConfigChange($data);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function cloudinaryConfigChanged(Varien_Event_Observer $observer)
    {
        //Clear config cache before mapping
        Mage::app()->getCacheInstance()->cleanType("config");
        Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => "config"));

        if (!Mage::getModel('cloudinary_cloudinary/configuration')->isEnabled()) {
            return;
        }

        if (!$this->autoUploadRequestProcessor()->handle('media', Mage::getBaseUrl('media'))) {
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
     * @param array $data
     */
    private function validateEnvironmentVariable(array $data)
    {
        $credentialValidator = new CredentialValidator();
        $environmentVariable = CloudinaryEnvironmentVariable::fromString($data[self::ENVIRONMENT_FIELD]);

        if (!$credentialValidator->validate($environmentVariable->getCredentials())) {
            throw new Mage_Core_Exception(self::ERROR_WRONG_CREDENTIALS);
        }
    }

    /**
     * @param array $data
     */
    private function logConfigChange(array $data)
    {
        $data[self::ENVIRONMENT_FIELD] = md5($data[self::ENVIRONMENT_FIELD]);
        Mage::getModel('cloudinary_cloudinary/logger')->notice(
            sprintf(self::CONFIG_CHANGE_MESSAGE, $this->formatConfigData($data))
        );
    }

    /**
     * @param array $data
     * @return string
     */
    private function formatConfigData(array $data)
    {
        return implode(
            ', ',
            array_map(
                function ($key, $value) {
                    return sprintf('(%s: %s)', $key, $value);
                },
                array_keys($data),
                array_values($data)
            )
        );
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
