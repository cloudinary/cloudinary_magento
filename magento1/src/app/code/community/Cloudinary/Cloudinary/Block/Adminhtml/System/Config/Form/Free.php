<?php

class Cloudinary_Cloudinary_Block_Adminhtml_System_Config_Form_Free extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cloudinary/system/config/free.phtml');
    }

    public function ajaxSampleSecretKey()
    {
        return Mage::getModel('adminhtml/url')->getSecretKey('cloudinaryajax', 'sample');
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return sprintf('%s%s', $element->getElementHtml(), $this->_toHtml());
    }
}
