<?php

use CloudinaryExtension\Image\Synchronizable;

class Cloudinary_Cloudinary_Model_Synchronisation extends Mage_Core_Model_Abstract implements Synchronizable
{

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/synchronisation');
    }

    public function tagAsSynchronized()
    {
        $this->setData('image_name', basename($this['value']));
        $this->setData('media_gallery_id', $this['value_id']);
        $this->unsetData('value_id');

        $this->save();
    }

    public function isImageInCloudinary($imageName)
    {
        $this->load($imageName, 'image_name');
        return !is_null($this->getId());
    }

    public function getFilename()
    {
        if (!$this->getValue()) {
            return null;
        }
        return $this->_baseMediaPath() . $this->getValue();
    }

    private function _baseMediaPath()
    {
        return Mage::getModel('catalog/product_media_config')->getBaseMediaPath();
    }
}