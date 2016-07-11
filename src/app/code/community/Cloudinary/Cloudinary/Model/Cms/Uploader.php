<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Cms_Uploader extends Mage_Core_Model_File_Uploader
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    protected function _afterSave($result)
    {
        parent::_afterSave($result);

        if ($this->canImageBeSynced($result)) {
            $imageProvider = CloudinaryImageProvider::fromConfiguration($this->_getConfigHelper()->buildConfiguration());
            $imageProvider->upload(Image::fromPath($result['path'] . DIRECTORY_SEPARATOR . $result['file']));
            $this->_trackSynchronisation($result['file']);
        }

        return $this;
    }

    private function _trackSynchronisation($fileName)
    {
        Mage::getModel('cloudinary_cloudinary/cms_synchronisation')
            ->setValue($fileName)
            ->tagAsSynchronized();
    }

    /**
     * Make sure we only sync images
     *
     * @param array $result
     * @return bool
     */
    private function canImageBeSynced($result) {
        if (empty($result['path'])) {
            return false;
        }
        if (empty($result['file'])) {
            return false;
        }
        if (empty($result['type'])) {
            return false;
        }
        if (strpos($result['type'], 'image') === false) {
            return false;
        }

        return true;
    }
}
