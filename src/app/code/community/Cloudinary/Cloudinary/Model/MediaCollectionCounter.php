<?php

class Cloudinary_Cloudinary_Model_MediaCollectionCounter implements Countable
{

    private $_collections = array();

    public function addCollection(Cloudinary_Cloudinary_Model_Resource_Media_Collection_Interface $collection)
    {
        $this->_collections[] = $collection;

        return $this;
    }

    public function count()
    {
        $mediaCount = 0;
        foreach ($this->_collections as $collection) {
            $mediaCount += $collection->getSize();
        }
        return $mediaCount;
    }

}
