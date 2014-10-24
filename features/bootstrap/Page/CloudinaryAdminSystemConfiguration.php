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
        'Save Config' => array('xpath' => '//*[@title="Save Config"]')
    );

    public function saveCredentials($aKey, $aSecret)
    {
        $this->getElement('Credentials Header')->click();
        $this->getElement('API Key')->setValue($aKey);
        $this->getElement('API Secret')->setValue($aSecret);
        $this->getElement('Save Config')->click();
    }
}
