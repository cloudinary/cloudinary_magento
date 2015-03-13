<?php

class Cloudinary_Cloudinary_Adminhtml_Cloudinary_ConsoleController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $redirectUrl = Mage::helper('cloudinary_cloudinary/console')->getMediaLibraryUrl();
        $configBlock = $this->_buildRedirectBlock($redirectUrl);

        $this->loadLayout();
        $this->getResponse()->setBody($configBlock->toHtml());
    }

    private function _buildRedirectBlock($redirectUrl)
    {
        return $this->getLayout()->createBlock('cloudinary_cloudinary/adminhtml_console_redirect')
            ->setRedirectUrl($redirectUrl)
            ->build();
    }

}