<?php
 
class Cloudinary_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract
{
    const REGISTERED_PATH = 'Cloudinary';

    public function onFrontInitBefore(Varien_Event_Observer $event)
    {
        $this->registerCloudinaryAutoloader();

        return $event;
    }

    public function onGalleryUploadAction(Varien_Event_Observer $event)
    {
        $uploadedFileDetails = $this->_getUploadedFileDetails($event);

        $cloudinayImage = Mage::getModel('cloudinary_cloudinary/image');
        $cloudinayImage->upload($uploadedFileDetails['file']);

        return $event;
    }

    protected function _getUploadedFileDetails($event)
    {
        $controllerAction = $event->getData('controller_action');
        $coreHelper = Mage::helper('core');
        return $coreHelper->jsonDecode($controllerAction->getResponse()->getBody());
    }

    protected function registerCloudinaryAutoloader()
    {
        spl_autoload_register(
            function ($class_name) {
                if(strpos($class_name, Cloudinary_Cloudinary_Model_Observer::REGISTERED_PATH . '\\') === 0) {
                    include_once preg_replace('#\\\|_(?!.*\\\)#', '/', $class_name) . '.php';
                }
            }
        );
    }
}