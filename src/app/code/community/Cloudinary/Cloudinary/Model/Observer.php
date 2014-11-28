<?php

class Cloudinary_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract
{
    const CLOUDINARY_EXTENSION_LIB_PATH = 'CloudinaryExtension';
    const CLOUDINARY_LIB_PATH = 'Cloudinary';
    const CONVERT_CLASS_TO_PATH_REGEX = '#\\\|_(?!.*\\\)#';

    private $originalAutoloaders;

    private $newImages;

    public function loadCustomAutoloaders(Varien_Event_Observer $event)
    {
        $this->deregisterVarienAutoloaders();
        $this->registerCloudinaryAutoloader();
        $this->registerCloudinaryExtensionAutoloader();
        $this->reregisterVarienAutoloaders();

        return $event;
    }

    public function uploadImageToCloudinary(Varien_Event_Observer $event)
    {
//        $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');
//        $image = $this->_getUploadedImageDetails($event);
//
//        $cloudinaryImage->upload($image);
//
//        $this->_deleteLocalFile($image);
//
        return $event;
    }

    public function updateCloudinarySyncStatus(Varien_Event_Observer $event)
    {
        $this->_setNewImages($event->getProduct());
        $newImages = $this->_getNewImages($event->getProduct());

        error_log(var_export($newImages, true));

        $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');
        foreach ($newImages as $image) {
            $cloudinaryImage->upload($image);
        }
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

    private function deregisterVarienAutoloaders()
    {
        $this->originalAutoloaders = array();

        foreach (spl_autoload_functions() as $callback) {
            if (is_array($callback) && $callback[0] instanceof Varien_Autoload) {
                $this->originalAutoloaders[] = $callback;
                spl_autoload_unregister($callback);
            }
        }
    }

    private function reregisterVarienAutoloaders()
    {
        foreach ($this->originalAutoloaders as $autoloader) {
            spl_autoload_register($autoloader);
        }
    }

    public function _isImageInArray($toFilter)
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
}