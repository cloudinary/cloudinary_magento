<?php
use CloudinaryExtension\CredentialValidator;
use CloudinaryExtension\Security\CloudinaryEnvironmentVariable;

/**
 * Cloudinary_Cloudinary_Model_System_Config_Credentials
 */
class Cloudinary_Cloudinary_Model_System_Config_Credentials extends Mage_Adminhtml_Model_System_Config_Backend_Encrypted
{
    /**
     * Encrypt value before saving
     *
     */
    protected function _beforeSave()
    {
        $value = (string)$this->getValue();
        if (preg_match('/^\*+$/', $value)) {
            $value = $this->getOldValue();
        }

        $isValid = false;
        try {
            $credentialValidator = new CredentialValidator();
            $environmentVariable = CloudinaryEnvironmentVariable::fromString($value);
            $isValid = (bool) $credentialValidator->validate($environmentVariable->getCredentials());
        } catch (\Exception $e) {
            //Ignore errors
        }

        if (!$isValid) {
            $this->setValue(null);
            Mage::getSingleton('core/session')->addError(Cloudinary_Cloudinary_Model_Observer_Config::ERROR_WRONG_CREDENTIALS);
        }

        parent::_beforeSave();

        Mage::register('cloudinaryEnvironmentVariable', $this->getValue());

        return $this;
    }
}
