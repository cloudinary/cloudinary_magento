<?php

class Cloudinary_Cloudinary_Model_Catalog_Product_Media extends Mage_Core_Model_Abstract
{
    private $_newImages;

    public function getProductMediaGallery(Mage_Catalog_Model_Product $product)
    {
        $mediaGallery = (array) $product->getData('media_gallery');
        $mediaGallery['images'] = isset($mediaGallery['images'])? $mediaGallery['images'] : [];
        if (!is_array($mediaGallery['images'])) {
            $mediaGallery['images'] = (array) @json_decode($mediaGallery['images'], true);
        }
        return $mediaGallery;
    }

    public function newImagesForProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_setNewImages($this->getProductMediaGallery($product));
        return $this->_getNewImages($product);
    }

    private function _setNewImages(array $mediaGallery)
    {
        $this->_newImages = array();
        foreach ($mediaGallery['images'] as $image) {
            if (array_key_exists('new_file', $image)) {
                $this->_newImages[] = $image['new_file'];
            }
        }
    }

    private function _getNewImages(Mage_Catalog_Model_Product $product)
    {
        $product->load('media_gallery');
        $mediaGallery = $this->getProductMediaGallery($product);
        return array_filter($mediaGallery['images'], array($this, '_isImageInArray'));
    }

    private function _isImageInArray($toFilter)
    {
        return is_array($toFilter) && array_key_exists('file', $toFilter) && in_array($toFilter['file'], $this->_newImages);
    }

    public function removedImagesForProduct(Mage_Catalog_Model_Product $product)
    {
        return $this->_getRemovedImages($this->getProductMediaGallery($product));
    }

    private function _getRemovedImages(array $mediaGallery)
    {
        return array_filter($mediaGallery['images'], array($this, '_isImageRemoved'));
    }

    private function _isImageRemoved($toFilter)
    {
        return is_array($toFilter) && array_key_exists('removed', $toFilter) && $toFilter['removed'] === 1;
    }
}
