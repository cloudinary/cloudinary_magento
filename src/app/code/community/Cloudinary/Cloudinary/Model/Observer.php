<?php

class Cloudinary_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract
{
    const CLOUDINARY_EXTENSION_LIB_PATH = 'CloudinaryExtension';
    const CLOUDINARY_LIB_PATH = 'Cloudinary';
    const CONVERT_CLASS_TO_PATH_REGEX = '#\\\|_(?!.*\\\)#';

    public function loadCustomAutoloaders(Varien_Event_Observer $event)
    {
        $this->registerCloudinaryAutoloader();
        $this->registerCloudinaryExtensionAutoloader();

        return $event;
    }

    public function uploadImageToCloudinary(Varien_Event_Observer $event)
    {
        $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');
        $image = $this->_getUploadedImageDetails($event);

        $cloudinaryImage->upload($image);

        $this->_deleteLocalFile($image);

        return $event;
    }

    protected function _getUploadedImageDetails($event)
    {
        return $event->getResult();
    }

    protected function registerCloudinaryExtensionAutoloader()
    {
        spl_autoload_register(
            function ($className) {
                if(
                    strpos($className, Cloudinary_Cloudinary_Model_Observer::CLOUDINARY_EXTENSION_LIB_PATH . '\\') === 0 ||
                    strpos($className, Cloudinary_Cloudinary_Model_Observer::CLOUDINARY_LIB_PATH . '\\') === 0
                ) {
                    include_once preg_replace(self::CONVERT_CLASS_TO_PATH_REGEX, '/', $className) . '.php';
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

    private function _deleteLocalFile($image)
    {
        $mediaConfig = new Mage_Catalog_Model_Product_Media_Config();
        $tmpPath = sprintf('%s%s', $mediaConfig->getBaseTmpMediaPath(), $image['file']);

        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }
    }
}