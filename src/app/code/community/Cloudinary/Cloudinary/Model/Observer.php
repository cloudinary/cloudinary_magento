<?php
 
class Cloudinary_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract
{
    const CLOUDINARY_EXTENSION_LIB_PATH = 'CloudinaryExtension';
    const CLOUDINARY_LIB_PATH = 'Cloudinary';

    public function onFrontInitBefore(Varien_Event_Observer $event)
    {
        $this->registerCloudinaryAutoloader();
        $this->registerCloudinaryExtensionAutoloader();

        return $event;
    }

    public function onGalleryUploadAction(Varien_Event_Observer $event)
    {
        $cloudinayImage = Mage::getModel('cloudinary_cloudinary/image');
        $cloudinayImage->upload($this->_getUploadedFileDetails($event));

        return $event;
    }

    protected function _getUploadedFileDetails($event)
    {
        $controllerAction = $event->getData('controller_action');
        $coreHelper = Mage::helper('core');
        return $coreHelper->jsonDecode($controllerAction->getResponse()->getBody());
    }

    protected function registerCloudinaryExtensionAutoloader()
    {
        spl_autoload_register(
            function ($className) {
                if(
                    strpos($className, Cloudinary_Cloudinary_Model_Observer::CLOUDINARY_EXTENSION_LIB_PATH . '\\') === 0 ||
                    strpos($className, Cloudinary_Cloudinary_Model_Observer::CLOUDINARY_LIB_PATH . '\\') === 0
                ) {
                    include_once preg_replace('#\\\|_(?!.*\\\)#', '/', $className) . '.php';
                }
            }
        );
    }

    protected function registerCloudinaryAutoloader()
    {
        $libFolder = Mage::getBaseDir('lib');

        spl_autoload_register(
            function ($className) use ($libFolder) {
                if($className ===  Cloudinary_Cloudinary_Model_Observer::CLOUDINARY_LIB_PATH) {
                    foreach(new GlobIterator($libFolder . DS . Cloudinary_Cloudinary_Model_Observer::CLOUDINARY_LIB_PATH . DS . '*.php') as $phpFile) {
                        include_once $phpFile;
                    }
                }
            }
        );
    }
}