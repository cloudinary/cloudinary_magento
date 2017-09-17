<?php

namespace Facade;

use \Mage;

class Magento
{
    /**
     * @param string $sku
     * @return Mage_Catalog_Model_Product
     * @throws \Exception
     */
    public function productWithSku($sku)
    {
        $product = Mage::getModel('catalog/product');
        $product->load($product->getIdBySku($sku));

        if (!$product->getId()) {
            throw new \Exception('Cannot find product with sku: ' . $sku);
        }

        return $product;
    }

    /**
     * @param string $sku
     * @return string
     * @throws \Exception
     */
    public function imagePathForProductWithSku($sku)
    {
        return $this->productWithSku($sku)->getImage();
    }

    /**
     * @param string $sku
     * @param string $imagePath
     * @throws \Exception
     */
    public function addImageToProductWithSku($sku, $imagePath)
    {
        Mage::app('default', 'store')->setCurrentStore(\Mage_Core_Model_App::ADMIN_STORE_ID);
        $product = $this->productWithSku($sku);
        $product->addImageToMediaGallery($imagePath, ['image', 'thumbnail', 'small_image'], false, false);
        $product->save();
    }

    /**
     * @param string $sku
     */
    public function deleteImagesFromProductWithSku($sku)
    {
        Mage::app('default', 'store')->setCurrentStore(\Mage_Core_Model_App::ADMIN_STORE_ID);
        $product = $this->productWithSku($sku);

        $galleryAttribute = \Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode($product->getEntityTypeId(), 'media_gallery');

        foreach ($product->getMediaGalleryImages() as $image) {
            $galleryAttribute->getBackend()->removeImage($product, $image->getFile());
        }

        $product->save();
    }

    /**
     * @param string $path
     * @param string $value
     */
    public function setConfig($path, $value)
    {
        Mage::app('default', 'store')->setCurrentStore(\Mage_Core_Model_App::ADMIN_STORE_ID);
        Mage::getConfig()->saveConfig($path, $value)->reinit();
        Mage::app()->getStore()->setConfig($path, $value);
        if (getenv('BEHAT_DEBUG')) {
            echo sprintf('Set config path: %s with value: %s%s', $path, $value, PHP_EOL);
        }
    }

    /**
     * @param string $path
     * @param string $value
     */
    public function setConfigEncrypted($path, $value)
    {
        Mage::app('default', 'store')->setCurrentStore(\Mage_Core_Model_App::ADMIN_STORE_ID);
        Mage::getConfig()->saveConfig($path, Mage::helper('core')->encrypt($value))->reinit();
        Mage::app()->getStore()->setConfig($path, Mage::helper('core')->encrypt($value));
        if (getenv('BEHAT_DEBUG')) {
            echo sprintf('Set config path: %s with encrypted value of: %s%s', $path, $value, PHP_EOL);
        }
    }
}
