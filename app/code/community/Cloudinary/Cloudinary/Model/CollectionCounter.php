<?php

class Cloudinary_Cloudinary_Model_CollectionCounter implements Countable
{
    private $_collections = array();

    public function addCollection(Varien_Data_Collection $collection)
    {
        $this->_collections[] = $collection;

        return $this;
    }

    public function count()
    {
        $count = 0;
        foreach ($this->_collections as $collection) {
            $count += $collection->getSize();
        }

        return $count;
    }
}
