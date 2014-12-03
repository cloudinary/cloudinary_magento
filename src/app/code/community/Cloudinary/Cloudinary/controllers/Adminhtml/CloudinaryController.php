<?php

class Cloudinary_Cloudinary_Adminhtml_CloudinaryController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $configBlock = $this->getLayout()->createBlock('cloudinary_cloudinary/adminhtml_manage');

        $this->loadLayout()
            ->_addContent($configBlock)
            ->renderLayout();
    }
}