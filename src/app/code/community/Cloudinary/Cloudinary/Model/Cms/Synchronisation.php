<?php

use CloudinaryExtension\Image\Synchronizable;

class Cloudinary_Cloudinary_Model_Cms_Synchronisation extends Mage_Core_Model_Abstract implements Synchronizable
{

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/synchronisation');
    }

    public function getFilename()
    {
        return $this->getData('filename');
    }

    public function setValue($fileName)
    {
        $this->setData('basename', basename($fileName));
        return $this;
    }

    public function getRelativePath(){
        $helperConfig = Mage::helper('cloudinary_cloudinary/configuration');
        return $helperConfig->getMigratedPath($this->getFilename());
    }

    public function tagAsSynchronized()
    {
        $this->setData('media_gallery_id', null);
        $this->setData('cloudinary_synchronisation_id', null);
        $this->setData('image_name', $this->getRelativePath());
        Cloudinary_Cloudinary_Helper_Loggerutil::log( json_encode($this->toArray(), JSON_PRETTY_PRINT));
        $this->save();
    }

}
