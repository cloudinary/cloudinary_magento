<?php


class Cloudinary_Cloudinary_Helper_Autoloader
{
    const CLOUDINARY_EXTENSION_LIB_PATH = 'CloudinaryExtension';
    const CLOUDINARY_LIB_PATH = 'Cloudinary';
    const CONVERT_CLASS_TO_PATH_REGEX = '#\\\|_(?!.*\\\)#';

    private $_originalAutoloaders;
    static protected $_isRegistered = false;

    public function register()
    {
        if (self::$_isRegistered) {
            return;
        }
        $this->_deregisterVarienAutoloaders();
        $this->_registerCloudinaryAutoloader();
        $this->_registerCloudinaryExtensionAutoloader();
        $this->_reregisterVarienAutoloaders();

        self::$_isRegistered = true;
    }

    private function _registerCloudinaryExtensionAutoloader()
    {

        spl_autoload_register(
            function ($className) {
                if(
                    strpos($className, self::CLOUDINARY_EXTENSION_LIB_PATH . '\\') === 0 ||
                    strpos($className, self::CLOUDINARY_LIB_PATH . '\\') === 0
                ) {
                    include_once preg_replace(self::CONVERT_CLASS_TO_PATH_REGEX, '/', $className) . '.php';
                }
            }
        );

        return $this;
    }

    private function _registerCloudinaryAutoloader()
    {
        $libFolder = Mage::getBaseDir('lib');

        spl_autoload_register(
            function ($className) use ($libFolder) {
                if($className ===  self::CLOUDINARY_LIB_PATH) {
                    foreach(new GlobIterator($libFolder . DS . self::CLOUDINARY_LIB_PATH . DS . '*.php') as $phpFile) {
                        include_once $phpFile;
                    }
                }
            }
        );

        return $this;
    }

    private function _deregisterVarienAutoloaders()
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
}
