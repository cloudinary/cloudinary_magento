<?php

class Cloudinary_Cloudinary_Model_Resource_Media_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('catalog/product_attribute_backend_media');
    }

    public function uniqueImageCount()
    {
        $table = $this->getMainTable();
        $query = "select count(distinct value) from $table";

        return $this->getConnection()->query($query)->fetchColumn();
    }
}
