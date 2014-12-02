<?php

class Cloudinary_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract
{
    const CLOUDINARY_EXTENSION_LIB_PATH = 'CloudinaryExtension';
    const CLOUDINARY_LIB_PATH = 'Cloudinary';
    const CONVERT_CLASS_TO_PATH_REGEX = '#\\\|_(?!.*\\\)#';

    private $_originalAutoloaders;

    public function loadCustomAutoloaders(Varien_Event_Observer $event)
    {
        $this->deregisterVarienAutoloaders();
        $this->_registerCloudinaryAutoloader();
        $this->_registerCloudinaryExtensionAutoloader();
        $this->_reregisterVarienAutoloaders();

        return $event;
    }

    public function uploadImagesToCloudinary(Varien_Event_Observer $event)
    {
        $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');

        foreach ($this->_getImagesToUpload($event->getProduct()) as $image) {
            $cloudinaryImage->upload($image);
        }
    }

    protected function _getUploadedImageDetails($event)
    {
        return $event->getResult();
    }

    protected function _registerCloudinaryExtensionAutoloader()
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

    protected function _registerCloudinaryAutoloader()
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

    private function deregisterVarienAutoloaders()
    {
        $this->_originalAutoloaders = array();

        foreach (spl_autoload_functions() as $callback) {
            if (is_array($callback) && $callback[0] instanceof Varien_Autoload) {
                $this->_originalAutoloaders[] = $callback;
                spl_autoload_unregister($callback);
            }
        }
    }

    private function _reregisterVarienAutoloaders()
    {
        foreach ($this->_originalAutoloaders as $autoloader) {
            spl_autoload_register($autoloader);
        }
    }

    private function _getImagesToUpload(Mage_Catalog_Model_Product $product)
    {
        $productMedia = Mage::getModel('cloudinary_cloudinary/catalog_product_media');
        return $productMedia->newImagesForProduct($product);
    }
}