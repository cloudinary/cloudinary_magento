<?php
 
class Cloudinary_Cloudinary_Model_Catalog_Product_Media extends Mage_Core_Model_Abstract
{

    private $newImages;

    public function newImagesForProduct(Mage_Catalog_Model_Product $product)
    {
        $this->setNewImages($product->getData('media_gallery'));
        return $this->getNewImages($product);
    }

    private function setNewImages(array $mediaGallery)
    {
        $this->newImages = array();

        foreach ($mediaGallery['images'] as $image) {
            if (array_key_exists('new_file', $image)) {
                $this->newImages[] = $image['new_file'];
            }
        }
    }

    private function getNewImages(Mage_Catalog_Model_Product $product)
    {
        $product->load('media_gallery');
        $gallery = $product->getData('media_gallery');
        return array_filter($gallery['images'], array($this, 'isImageInArray'));
    }

    private function isImageInArray($toFilter)
    {
        return is_array($toFilter) && array_key_exists('file', $toFilter) && in_array($toFilter['file'], $this->newImages);
    }
}