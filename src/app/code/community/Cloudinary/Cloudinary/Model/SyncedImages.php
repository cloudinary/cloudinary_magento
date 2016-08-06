<?php

class Cloudinary_Cloudinary_Model_SyncedImages
{
    protected $_syncronisation;
    protected $_syncedImages = [];

    public function __construct($arguments)
    {
        if(!isset($arguments['synchronisation'])) {
            $arguments['synchronisation'] = Mage::getModel('cloudinary_cloudinary/synchronisation');
        }
        $this->_syncronisation = $arguments['synchronisation'];

    }

    public function isImageInCloudinary($imageName)
    {
        if (!isset($this->_syncedImages[$imageName])) {
            $coll = $this->_syncronisation->getCollection();
            $table = $coll->getMainTable();
            // case sensitive check

            $query = "select 1 from $table where binary image_name = '$imageName' limit 1";

            $this->_syncedImages[$imageName] = ($coll->getConnection()->query($query)->fetchColumn() > 0);
        }

        return $this->_syncedImages[$imageName];
    }
}
