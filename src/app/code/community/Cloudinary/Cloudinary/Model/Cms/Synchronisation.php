<?php

use CloudinaryExtension\Image\Synchronizable;

class Cloudinary_Cloudinary_Model_Cms_Synchronisation
    extends Mage_Core_Model_Abstract
    implements Synchronizable
{

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/synchronisation');
    }

    public function getFilename()
    {
        return $this->getData('filename');
    }

    public function getRelativePath(){
        return Mage::getModel('cloudinary_cloudinary/configuration')->getMigratedPath($this->getFilename());
    }

    public function tagAsSynchronized()
    {
        $this->setData('media_gallery_id', null);
        $this->setData('cloudinary_synchronisation_id', null);
        $this->setData('image_name', $this->getRelativePath());

        $this->save();
    }
}
