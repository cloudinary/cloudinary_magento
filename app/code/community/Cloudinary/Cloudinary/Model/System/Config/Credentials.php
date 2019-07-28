<?php

use CloudinaryExtension\CredentialValidator;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;
use Cloudinary\Api;
use CloudinaryExtension\ConfigurationBuilder;
use CloudinaryExtension\ConfigurationInterface;
use Magento\Framework\Exception\ValidatorException;
use CloudinaryExtension\Credentials as CredentialsValue;
use CloudinaryExtension\Exception\InvalidCredentials;

/**
 * Cloudinary_Cloudinary_Model_System_Config_Credentials
 */
class Cloudinary_Cloudinary_Model_System_Config_Credentials extends Mage_Adminhtml_Model_System_Config_Backend_Encrypted
{
    const CREDENTIALS_CHECK_MISSING = 'You must provide Cloudinary credentials.';
    const CREDENTIALS_CHECK_FAILED = 'Your Cloudinary credentials are not correct.';
    const CREDENTIALS_CHECK_UNSURE = 'There was a problem validating your Cloudinary credentials.';
    const CLOUDINARY_ENABLED_PATH = 'groups/cloud/fields/cloudinary_enabled/value';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ConfigurationBuilder
     */
    private $configurationBuilder;

    /**
     * @var Cloudinary\Api
     */
    private $api;

    /**
     * @param string $resourceModel
     */
    protected function _init($resourceModel)
    {
        $this->configuration = Mage::getModel('cloudinary_cloudinary/configuration');
        $this->configurationBuilder = new ConfigurationBuilder($this->configuration);
        $this->api = new Api();
        return parent::_init($resourceModel);
    }

    /**
     * Encrypt value before saving
     *
     */
    protected function _beforeSave()
    {
        $rawValue = (string)$this->getValue();

        $isSaveAllowed = true;
        if (preg_match('/^\*+$/', $rawValue)) {
            $isSaveAllowed = false;
            $rawValue = $this->getOldValue();
        }

        parent::_beforeSave();

        //Clear config cache before mapping
        Mage::app()->getCacheInstance()->cleanType("config");
        Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => "config"));
        Mage::getConfig()->reinit();

        if ($rawValue || $this->configuration->isEnabled()) {
            if (!$rawValue) {
                throw new Mage_Core_Exception(__(self::CREDENTIALS_CHECK_MISSING));
            }
            if ($isSaveAllowed) {
                $this->validate($this->getCredentialsFromEnvironmentVariable($rawValue));
            } else {
                $this->validate($this->getCredentialsFromConfig());
            }
        }

        Mage::register('cloudinaryEnvironmentVariable', $this->getValue());

        return $this;
    }


    /**
     * @param array $credentials
     * @throws Mage_Core_Exception
     */
    private function validate(array $credentials)
    {
        $this->_authorise($credentials);
        $pingValidation = $this->api->ping();
        if (!(isset($pingValidation["status"]) && $pingValidation["status"] === "ok")) {
            $this->setValue(null);
            throw new Mage_Core_Exception(__(self::CREDENTIALS_CHECK_UNSURE));
        }
    }

    /**
     * @param string $environmentVariable
     * @throws Mage_Core_Exception
     * @return array
     */
    private function getCredentialsFromEnvironmentVariable($environmentVariable)
    {
        try {
            Cloudinary::config_from_url(str_replace('CLOUDINARY_URL=', '', $environmentVariable));
            $credentials = [
                "cloud_name" => Cloudinary::config_get('cloud_name'),
                "api_key" => Cloudinary::config_get('api_key'),
                "api_secret" => Cloudinary::config_get('api_secret')
            ];
            if (Cloudinary::config_get('private_cdn')) {
                $credentials["private_cdn"] = Cloudinary::config_get('private_cdn');
            }
            return $credentials;
        } catch (\Exception $e) {
            throw new Mage_Core_Exception(__(self::CREDENTIALS_CHECK_FAILED));
        }
    }

    /**
     * @throws ValidatorException
     * @return array
     */
    private function getCredentialsFromConfig()
    {
        try {
            return $this->getCredentialsFromEnvironmentVariable($this->configuration->getEnvironmentVariable()->__toString());
        } catch (InvalidCredentials $e) {
            throw new Mage_Core_Exception(__(self::CREDENTIALS_CHECK_FAILED));
        }
    }

    /**
     * @param array $credentials
     */
    private function _authorise(array $credentials)
    {
        Cloudinary::config($credentials);
        Cloudinary::$USER_PLATFORM = $this->configuration->getUserPlatform();
    }
}
