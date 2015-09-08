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
        $this->setData('image_name', $this->getRelativePath());
        $this->setData('media_gallery_id', $this['value_id']);
        $this->setData('media_gallery_value', $this['value']);
        $this->unsetData('value_id');
        Cloudinary_Cloudinary_Model_Logger::getInstance()->debugLog( json_encode($this->toArray(), JSON_PRETTY_PRINT));
        $this->save();
    }

    public function isImageInCloudinary($imageName)
    {
        $coll = $this->getCollection();
        $table = $coll->getMainTable();
        // case sensitive check
        $query = "select count(*) from $table where binary image_name = '$imageName' limit 1";
        return $coll->getConnection()->query($query)->fetchColumn() > 0;
    }

    public function getFilename()
    {
        if (!$this->getValue()) {
            return null;
        }
        return $this->_baseMediaPath() . $this->getValue();
    }


    public function getRelativePath()
    {
        $helperConfig = Mage::helper('cloudinary_cloudinary/configuration');
        return $helperConfig->getMigratedPath($this->getFilename());
    }

    private function _baseMediaPath()
    {
        return Mage::getModel('catalog/product_media_config')->getBaseMediaPath();
    }
}
