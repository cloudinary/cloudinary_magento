<?php

class Cloudinary_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract
{


    public function loadCustomAutoloaders(Varien_Event_Observer $event)
    {
        Mage::helper('cloudinary_cloudinary/autoloader')->register();

        return $event;
    }

    public function uploadImageToCloudinary(Varien_Event_Observer $event)
    {
        $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');
        $image = $this->_getUploadedImageDetails($event);

        $cloudinaryImage->upload($image);

        $this->_deleteLocalFile($image);

        return $event;
    }

    protected function _getUploadedImageDetails($event)
    {
        return $event->getResult();
    }


    private function _deleteLocalFile($image)
    {
        $mediaConfig = new Mage_Catalog_Model_Product_Media_Config();
        $tmpPath = sprintf('%s%s', $mediaConfig->getBaseTmpMediaPath(), $image['file']);

        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }
    }

}