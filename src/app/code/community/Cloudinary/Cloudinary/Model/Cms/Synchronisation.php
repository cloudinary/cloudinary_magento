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

    public function tagAsSynchronized()
    {
        $this->setData('image_name', $this->getData('basename'));
        $this->setData('media_gallery_id', null);
        $this->setData('cloudinary_synchronisation_id', null);

        $this->save();
    }

}