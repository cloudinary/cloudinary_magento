<?php

class Cloudinary_Cloudinary_Block_Adminhtml_Reset_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'cloudinary_cloudinary';
        $this->_controller = 'adminhtml_reset';
        $this->_headerText = Mage::helper('cloudinary_cloudinary')->__('Reset all Cloudinary module data');
        $this->setTemplate('cloudinary/reset.phtml');

        $this->updateButton('save', 'label', 'Reset all Cloudinary data');
        $this->updateButton('save', 'area', 'footer');
        $this->updateButton('save', 'onclick', 'openCloudinaryResetConfirmation();');
    }
}
