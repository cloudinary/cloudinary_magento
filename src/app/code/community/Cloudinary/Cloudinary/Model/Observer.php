<?php

class Cloudinary_Cloudinary_Model_Observer extends Mage_Core_Model_Abstract
{


    public function loadCustomAutoloaders(Varien_Event_Observer $event)
    {
        Mage::helper('cloudinary_cloudinary/autoloader')->register();

        return $event;
    }

    public function uploadImagesToCloudinary(Varien_Event_Observer $event)
    {
        $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');

        foreach ($this->_getImagesToUpload($event->getProduct()) as $image) {
            $cloudinaryImage->upload($image);
        }
    }

    private function _getImagesToUpload(Mage_Catalog_Model_Product $product)
    {
        $productMedia = Mage::getModel('cloudinary_cloudinary/catalog_product_media');
        return $productMedia->newImagesForProduct($product);
    }
}