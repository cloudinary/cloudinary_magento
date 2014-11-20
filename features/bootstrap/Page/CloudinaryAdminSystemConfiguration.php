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
        'Save Config' => array('xpath' => '//*[@title="Save Config"]')
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
}
