<?php

trait  Cloudinary_Cloudinary_Model_PreConditionsValidator
{
    private function _isEnabled()
    {
        return $this->_getConfigHelper()->isEnabled();
    }

    private function _isImageInCloudinary($imageName)
    {
        return Mage::getModel('cloudinary_cloudinary/synchronisation')->isImageInCloudinary($imageName);
    }

    /**
     * @return Cloudinary_Cloudinary_Helper_Configuration
     */
    private function _getConfigHelper()
    {
        return Mage::helper('cloudinary_cloudinary/configuration');
    }

    private function _imageShouldComeFromCloudinary($file)
    {
        $relativePath = $this->_getConfigHelper()->getMigratedPath($file);
        $result = $this->_isEnabled() && $this->_isImageInCloudinary($relativePath);
        return $result;
    }
}
