<?php
 
class Cloudinary_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract
{

    public function onGalleryUploadAction(Varien_Event_Observer $event)
    {
        $uploadedFileDetails = $this->_getUploadedFileDetails();

        $cloudinayImage = Mage::getModel('cloudinary_cloudinary/image');
        $cloudinayImage->upload($uploadedFileDetails['file']);
    }

    private function _getUploadedFileDetails()
    {
        $controllerAction = $event->getData('controller_action');
        $coreHelper = Mage::helper('core');
        return $coreHelper->jsonDecode($controllerAction->getResponse()->getBody());
    }
}