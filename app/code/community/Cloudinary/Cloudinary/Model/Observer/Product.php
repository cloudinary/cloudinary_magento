<?php

use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Image;
use Mage_Catalog_Model_Product as Product;

class Cloudinary_Cloudinary_Model_Observer_Product extends Mage_Core_Model_Abstract
{
    const DELETE_MESSAGE = 'deleted product image from Cloudinary: %s';

    /**
     * @param Varien_Event_Observer $event
     */
    public function uploadImagesToCloudinary(Varien_Event_Observer $event)
    {
        if (Mage::getModel('cloudinary_cloudinary/configuration')->isEnabled()) {
            $cloudinaryImage = Mage::getModel('cloudinary_cloudinary/image');

            foreach ($this->getImagesToUpload($event->getProduct()) as $image) {
                $cloudinaryImage->upload($image);
            }
        }
    }

    /**
     * @param Varien_Event_Observer $event
     */
    public function deleteImagesFromCloudinary(Varien_Event_Observer $event)
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = Mage::getModel('cloudinary_cloudinary/configuration');

        if (!$configuration->isEnabled()) {
            return;
        }

        $imageProvider = CloudinaryImageProvider::fromConfiguration($configuration);

        foreach ($this->getImagesToDelete($event->getProduct()) as $image) {
            $migratedPath = $configuration->isFolderedMigration() ? $configuration->getMigratedPath($image['file']) : '';
            $imageProvider->delete(Image::fromPath($image['file'], ltrim($migratedPath, '/')));
            Mage::getModel('cloudinary_cloudinary/logger')->notice(sprintf(self::DELETE_MESSAGE, $image['file']));
        }
    }

    /**
     * @param Product $product
     * @return array
     */
    private function getImagesToUpload(Product $product)
    {
        return Mage::getModel('cloudinary_cloudinary/catalog_product_media')->newImagesForProduct($product);
    }

    /**
     * @param Product $product
     * @return array
     */
    private function getImagesToDelete(Product $product)
    {
        $productMedia = Mage::getModel('cloudinary_cloudinary/catalog_product_media');
        return $productMedia->removedImagesForProduct($product);
    }
}
