<?php

class Cloudinary_Cloudinary_Model_MagentoFolderTranslator implements Cloudinary_Cloudinary_Model_FolderTranslator
{
    /**
     * @param $folder
     * @return mixed the input, truncated from the magento base directory
     */
    public function translate($folder)
    {
        $baseDirRegex = sprintf('#^%s#', Mage::getBaseDir());
        $result = preg_replace($baseDirRegex, '', $folder);
        return $result;
    }

    public function reverse($folder)
    {
        $result = Mage::getBaseDir() . DIRECTORY_SEPARATOR . $folder;
        $doubleSeparator = DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR;
        return str_replace($doubleSeparator, DIRECTORY_SEPARATOR, $result);
    }
}
