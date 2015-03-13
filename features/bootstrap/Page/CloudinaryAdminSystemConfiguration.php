<?php

namespace Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CloudinaryAdminSystemConfiguration extends Page
{
    protected $path = '/index.php/admin/system_config/edit/section/cloudinary/';

    protected $elements = array(
        'Credentials Header' => array('xpath' => '//*[@id="cloudinary_credentials-head"]'),
        'API Key' => array('xpath' => '//*[@id="cloudinary_credentials_cloudinary_api_key"]'),
        'API Secret' => array('xpath' => '//*[@id="cloudinary_credentials_cloudinary_api_secret"]'),
        'Cloud Header' => array('xpath' => '//*[@id="cloudinary_cloud-head"]'),
        'Cloud Name' => array('xpath' => '//*[@id="cloudinary_cloud_cloudinary_cloud_name"]'),
        'Save Config' => array('xpath' => '//*[@title="Save Config"]'),
        'Image Transformations Header' => array('xpath' => '//*[@id="cloudinary_transformations-head"]'),
        'Default Gravity for Images' => array('css' => "#cloudinary_transformations_cloudinary_gravity option[selected='selected']"),
        'Sign Up Prompt' => array('xpath' => '//*[@id="config_edit_form"]//h3[contains(text(), "Haven\'t got a Cloudinary Account?")]'),
    );

    public function enterCredentials($aKey, $aSecret)
    {
        $this->getElement('Credentials Header')->click();
        $this->getElement('API Key')->setValue($aKey);
        $this->getElement('API Secret')->setValue($aSecret);
    }

    public function enterCloudName($aCloud)
    {
        $this->getElement('Cloud Header')->click();
        $this->getElement('Cloud Name')->setValue($aCloud);
    }

    public function saveCloudinaryConfiguration()
    {
        $this->getElement('Save Config')->click();
    }

    public function getSelectedGravity()
    {
        return $this->getElement('Default Gravity for Images')->getHtml();
    }

    public function containsSignUpPrompt()
    {
        return $this->hasElement('Sign Up Prompt');
    }
}
