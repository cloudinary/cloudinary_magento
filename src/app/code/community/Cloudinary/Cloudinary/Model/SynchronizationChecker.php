<?php

use CloudinaryExtension\Image\SynchronizationChecker as SynchronizationCheckerInterface;

class Cloudinary_Cloudinary_Model_SynchronizationChecker implements SynchronizationCheckerInterface
{
    public function isSynchronized($imageName)
    {
        if (!$imageName) {
            return false;
        }
        $coll = Mage::getModel('cloudinary_cloudinary/synchronisation')->getCollection();
        $table = $coll->getMainTable();
        // case sensitive check
        $query = "select count(*) from $table where binary image_name = '$imageName' limit 1";
        return $coll->getConnection()->query($query)->fetchColumn() > 0;
    }
}
