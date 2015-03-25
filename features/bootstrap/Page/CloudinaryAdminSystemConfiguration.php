<?php

namespace Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CloudinaryAdminSystemConfiguration extends Page
{
    protected $path = '/index.php/admin/system_config/edit/section/cloudinary/';

    protected $elements = array(
        'Setup Header' => array('xpath' => '//*[@id="cloudinary_setup-head"]'),
        'Environment Variable' => array('xpath' => '//*[@id="cloudinary_setup_cloudinary_environment_variable"]'),
        'Save Config' => array('xpath' => '//*[@title="Save Config"]'),
        'Image Transformations Header' => array('xpath' => '//*[@id="cloudinary_transformations-head"]'),
        'Default Gravity for Images' => array('css' => "#cloudinary_transformations_cloudinary_gravity option[selected='selected']"),
        'Sign Up Prompt' => array('xpath' => '//*[@id="config_edit_form"]//h3[contains(text(), "Haven\'t got a Cloudinary Account?")]'),
    );

    public function enterEnvironmentVariable($anEnvironmentVariable)
    {
        $this->getElement('Setup Header')->click();
        $this->getElement('Environment Variable')->setValue($anEnvironmentVariable);
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
