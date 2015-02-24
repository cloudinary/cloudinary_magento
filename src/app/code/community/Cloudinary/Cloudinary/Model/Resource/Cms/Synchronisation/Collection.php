<?php

use CloudinaryExtension\Migration\SynchronizedMediaRepository;

class Cloudinary_Cloudinary_Model_Resource_Cms_Synchronisation_Collection
    extends Mage_Cms_Model_Wysiwyg_Images_Storage_Collection
    implements SynchronizedMediaRepository, Cloudinary_Cloudinary_Model_Resource_Media_Collection_Interface
{

    public function __construct()
    {
        $this->addTargetDir(Mage::helper('cms/wysiwyg_images')->getStorageRoot());
        $this->setItemObjectClass('cloudinary_cloudinary/cms_synchronisation');
    }

    public function findUnsynchronisedImages()
    {
        $this->addFieldToFilter('basename', array('nin' => $this->_getSynchronisedImageNames()));

        return $this->getItems();
    }

    private function _getSynchronisedImageNames()
    {
        return array_map(
            function ($itemData) {
                return $itemData['image_name'];
            },
            $this->_getSynchronisedImageData()
        );
    }

    private function _getSynchronisedImageData()
    {
        return Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection')
            ->addFieldToSelect('image_name')
            ->addFieldToFilter('media_gallery_id', array('null' => true))
            ->distinct(true)
            ->getData();
    }

}