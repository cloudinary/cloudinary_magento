<?php

class Cloudinary_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract implements Cloudinary_Cloudinary_Model_Enablable
{
    const CLOUDINARY_EXTENSION_LIB_PATH = 'CloudinaryExtension';
    const CLOUDINARY_LIB_PATH = 'Cloudinary';
    const CONVERT_CLASS_TO_PATH_REGEX = '#\\\|_(?!.*\\\)#';

    private $_originalAutoloaders;
    private $newImages;
    private $_config;

    public function __construct()
    {
        $this->_config = Mage::helper('cloudinary_cloudinary/configuration');
    }

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
        if($this->isEnabled()) {
            $this->_setNewImages($event->getProduct());
            $newImages = $this->_getNewImages($event->getProduct());

            $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');
            foreach ($newImages as $image) {
                $cloudinaryImage->upload($image);
            }
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

    private function _isImageInArray($toFilter)
    {
        return is_array($toFilter) && array_key_exists('file', $toFilter) && in_array($toFilter['file'], $this->newImages);
    }

    private function _setNewImages(Mage_Catalog_Model_Product $product)
    {
        $this->newImages = array();

        $gallery = $product->getData('media_gallery');
        foreach ($gallery['images'] as $image) {
            if (array_key_exists('new_file', $image)) {
                $this->newImages[] = $image['new_file'];
            }
        }
        return $product;
    }

    private function _getNewImages($product)
    {
        $product->load('media_gallery');
        $gallery = $product->getData('media_gallery');
        $newImages = array_filter($gallery['images'], array($this, '_isImageInArray'));
        return $newImages;
    }

    public function isEnabled()
    {
        if(is_null($this->_config)) {
            $this->_config = Mage::helper('cloudinary_cloudinary/configuration');
        }

        return $this->_config->isEnabled();
    }
}