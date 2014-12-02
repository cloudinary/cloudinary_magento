<?php

class Cloudinary_Cloudinary_Adminhtml_CloudinaryController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $configBlock = $this->getLayout()->createBlock('cloudinary_cloudinary/adminhtml_manage');
////        $this->_setActiveMenu('cloudinary_cloudinary/manage');
//
//        $this->loadLayout()
//            ->_addContent($this->getLayout()
//                ->createBlock('core/text', 'example-block')
//                ->setText('<h1>This is a text block</h1>'))
//            ->renderLayout();
    }
}