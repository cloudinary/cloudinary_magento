<?php

/**
 * Class Cloudinary_Cloudinary_Model_CollectionCounter
 */
class Cloudinary_Cloudinary_Model_CollectionCounter implements Countable
{
    /**
     * @var Varien_Data_Collection[]
     */
    private $_collections = [];

    /**
     * @param Varien_Data_Collection $collection
     *
     * @return $this
     */
    public function addCollection(Varien_Data_Collection $collection)
    {
        $this->_collections[] = $collection;

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        $count = 0;
        foreach ($this->_collections as $collection) {
            $count += $collection->getSize();
        }
        return $count;
    }
}
