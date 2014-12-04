<?php

class Cloudinary_Cloudinary_Model_Synchronisation extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('cloudinary_cloudinary/synchronisation');
    }

    public function tagImageAsBeingInCloudinary($imageDetails)
    {
        $this->setData('image_name', $imageDetails['image_name']);
        $this->setData('media_gallery_id', $imageDetails['media_gallery_id']);
        $this->save();
    }

    public function isImageInCloudinary($imageName)
    {
        $this->load($imageName, 'image_name');
        return !is_null($this->getMediaGalleryId());
    }
}