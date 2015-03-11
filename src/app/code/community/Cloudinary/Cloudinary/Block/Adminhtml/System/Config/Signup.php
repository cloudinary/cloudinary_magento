<?php

class Cloudinary_Cloudinary_Block_Adminhtml_System_Config_Signup extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    protected function _construct()
    {
        $this->setTemplate('cloudinary/system/config/signup.phtml');
        parent::_construct();
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if (!$this->_cloudAndCredentialsArePresentInConfig()) {
            return $this->toHtml();
        }
    }

    private function _cloudAndCredentialsArePresentInConfig()
    {
        $configuration = Mage::helper('cloudinary_cloudinary/configuration');
        return $configuration->getApiKey() && $configuration->getApiSecret() && $configuration->getCloudName();
    }

}