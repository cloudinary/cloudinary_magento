<?php

class Cloudinary_Cloudinary_Model_MagentoFolderTranslator implements \CloudinaryExtension\FolderTranslator
{
    private $absolutePathRegex;
    private $wysiwygPathRegex;
    private $productPathRegex;
    private $mediaDir;

    public function __construct()
    {
        $baseDir = Mage::getBaseDir();
        $mediaDir = $this->mediaDir = $this->removeMagentoBaseDir(Mage::getBaseDir('media'));
        $this->absolutePathRegex = "#^$baseDir/.*#"; //anything starting with the magento base dir
        $this->mediaPathRegex = "#^$mediaDir/.*#"; //anything starting with the media folder
        $this->wysiwygPathRegex = "#^/wysiwyg/.*#"; // anything starting with the /wysiwyg folder
        $this->productPathRegex = "#(^/cache/.*|^/(\\w/){2})#"; // any path that has '/cache/' prefix, or '/l/l/' structure, where l is a single letter
    }

    /**
     * The method tries to find out the absolute path of the input (this method is typically called via a product image path, a wysiwyg path, or an absolute path)
     *
     * @param $path
     * @return mixed the input, truncated from the magento base directory
     */
    public function translate($path)
    {
        $baseName = basename($path);
        $result = $this->unifiedDirName($path);
        $debug = $result;

        $baseDir = Mage::getBaseDir();

        if (preg_match($this->absolutePathRegex, $result)) {
            // the input is absolute, we truncate the magento base dir to get the relative path
            $result = preg_replace("#^$baseDir#", '', $result);

        } else if (preg_match($this->productPathRegex, $result)) {
            /* the input appears to be a product image, we insert the path to product images relative to magento base dir
             * (by default /media/catalog/product/ ) */
            $catalogMediapath = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
            $result = $this->removeMagentoBaseDir($catalogMediapath) . $result;

        } else if (preg_match($this->mediaPathRegex, $result)) {
            // the input appears to be relative to the magento base dir
            // NOP, the result should be the input

        } else {
            // we just assume the input is relative to the media library, in which case the relative path is "/media/$path" (in defualt case)
            $result = $this->mediaDir . $result;
        }
        $result .= $baseName;
        Cloudinary_Cloudinary_Model_Logger::getInstance()->debugLog("$path => $debug => $result");
        return $result;
    }

    public function reverse($path)
    {
        return str_replace(DS . DS, DS, Mage::getBaseDir() . DS . $path);
    }

    /**
     * Appends DS the the start of the path, and removes duplicate DS-es
     *
     * @param $path
     * @return mixed
     */
    private static function unifiedDirName($path)
    {
        return str_replace(DS . DS, DS, DS . dirname($path) . DS);
    }

    /**
     * @param $path an absolute path pointing somewhere inside magento folder structure
     * @return mixed the path relative to the magento base directory
     */
    private static function removeMagentoBaseDir($path)
    {
        return str_replace(Mage::getBaseDir(), '', $path);
    }
}
