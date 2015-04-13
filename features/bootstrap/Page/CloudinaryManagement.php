<?php

namespace Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CloudinaryManagement extends Page
{
    protected $path = '/index.php/admin/cloudinary/';

    protected $elements = array(
        'Enable Button' => array('css' => 'button[title="Enable Cloudinary"]'),
        'Disable Button' => array('css' => 'button[title="Disable Cloudinary"]'),
    );

    public function enable()
    {
        $this->getElement('Enable Button')->click();
    }

    public function disable()
    {
        $this->getElement('Disable Button')->click();
    }

    public function hasDisableButton()
    {
        return $this->hasElement('Disable Button');
    }

    public function hasEnableButton()
    {
        return $this->hasElement('Enable Button');
    }
}
