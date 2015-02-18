<?php

use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cms_Uploader extends Mage_Core_Model_File_Uploader
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    protected function _afterSave($result)
    {
        parent::_afterSave($result);

        if (!empty($result['path']) && !empty($result['file'])) {
            $imageManager = ImageManagerFactory::buildFromConfiguration(
                Mage::helper('cloudinary_cloudinary/configuration')->buildConfiguration()
            );

            $imageManager->uploadImage($result['path'] . DIRECTORY_SEPARATOR . $result['file']);

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
}