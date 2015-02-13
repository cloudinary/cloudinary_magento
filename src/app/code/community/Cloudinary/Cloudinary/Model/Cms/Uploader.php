<?php

use CloudinaryExtension\ImageManagerFactory;

class Cloudinary_Cloudinary_Model_Cms_Uploader extends Mage_Core_Model_File_Uploader
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function save($destinationFolder, $newFileName = null)
    {
        parent::save($destinationFolder, $newFileName);
        $imageManager = ImageManagerFactory::buildFromConfiguration(
            Mage::helper('cloudinary_cloudinary/configuration')->buildConfiguration()
        );

        $imageManager->uploadImage($this->_result['path'] . DIRECTORY_SEPARATOR . $this->_result['file']);
    }
}