<?php
 
class Cloudinary_Cloudinary_Model_Catalog_Product_Media extends Mage_Core_Model_Abstract
{

    private $_newImages;

    public function newImagesForProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_setNewImages($product->getData('media_gallery'));
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
        $gallery = $product->getData('media_gallery');
        return array_filter($gallery['images'], array($this, '_isImageInArray'));
    }

    private function _isImageInArray($toFilter)
    {
        return is_array($toFilter) && array_key_exists('file', $toFilter) && in_array($toFilter['file'], $this->_newImages);
    }
}