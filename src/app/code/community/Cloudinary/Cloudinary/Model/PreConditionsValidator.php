<?php
trait  Cloudinary_Cloudinary_Model_PreConditionsValidator
{
    private $_config;

    private function _isEnabled()
    {
        return $this->_getConfigHelper()->isEnabled();
    }

    private function _isImageInCloudinary($imageName)
    {
        return Mage::getModel('cloudinary_cloudinary/synchronisation')->isImageInCloudinary($imageName);
    }

    private function _getConfigHelper()
    {
        return $this->_config = Mage::helper('cloudinary_cloudinary/configuration');
    }

    private function _imageShouldComeFromCloudinary($file)
    {
        return $this->_isEnabled() && $this->_isImageInCloudinary($file);
    }
}
 