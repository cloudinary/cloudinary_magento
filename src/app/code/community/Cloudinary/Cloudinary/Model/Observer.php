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
        if (Mage::helper('cloudinary_cloudinary/configuration')->isEnabled()) {
            $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');

            foreach ($this->_getImagesToUpload($event->getProduct()) as $image) {
                $cloudinaryImage->upload($image);
            }
        }
    }

    private function _getImagesToUpload(Mage_Catalog_Model_Product $product)
    {
        return Mage::getModel('cloudinary_cloudinary/catalog_product_media')->newImagesForProduct($product);
    }
}