<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'abstract.php';

/**
 * Seek and eliminate invalid product image database etries
 */
class Product_Image_Cleanup extends Mage_Shell_Abstract
{
    const PRODUCT_MEDIA_GALERY_TABLE = 'catalog_product_entity_media_gallery';
    const REMOVED_PRODUCT_IMAGE_ENTRIES_LOG = 'removed_product_image_enties.log';
    const MEDIA_GALLERY_ATTRIBUTE = 'media_gallery';

    protected $_productCollection;
    protected $_connection;
    protected $_resource;
    protected $_removedImageData = [];

    /**
     * Constructor
     */
    public function _construct()
    {
        $this->_resource = Mage::getModel('core/resource');
        $this->_connection = $this->_resource->getConnection(
            Mage_Core_Model_Resource::DEFAULT_WRITE_RESOURCE
        );
        $this->_productCollection = $this->_getProductCollection();
        Mage::helper('cloudinary_cloudinary/autoloader')->register();
    }

    /**
     * Entry point of the shell script
     */
    public function run()
    {
        $mediaBackend = Mage::getModel('catalog/product_attribute_backend_media');
        $mediaGalleryAttribute = Mage::getModel('eav/config')->getAttribute(
            Mage::getModel('catalog/product')->getResource()->getTypeId(),
            self::MEDIA_GALLERY_ATTRIBUTE
        );
        $mediaBackend->setAttribute($mediaGalleryAttribute);
        foreach ($this->_productCollection as $product) {
            $mediaBackend->afterLoad($product);
            $this->_checkImageFiles($product);
        }

        if (count($this->_removedImageData)) {
            Mage::log($this->_removedImageData, Zend_Log::INFO, self::REMOVED_PRODUCT_IMAGE_ENTRIES_LOG);
        }
    }

    protected function _getProductCollection()
    {
        return Mage::getModel('catalog/product')->getCollection();
    }

    protected function _checkImageFiles($product)
    {
        foreach ($product->getData('media_gallery')['images'] as $productImageData) {
            $imageFullPath = Mage::getBaseDir('media') . DS . 'catalog/product' . $productImageData['file'];
            if (!file_exists($imageFullPath)) {
                $this->_removeInvalidProductImageDbEntry($productImageData, $product->getId());
            }
        }
    }

    protected function _removeInvalidProductImageDbEntry($productImageData, $productId)
    {
        $tableName = $this->_resource->getTableName(self::PRODUCT_MEDIA_GALERY_TABLE);
        $this->_connection->delete($tableName, sprintf('value_id = %s', $productImageData['value_id']));
        $this->_removedImageData[] = ['file_path' => $productImageData['file'], 'product_id' => $productId];
    }
}

$productImageCleanup = new Product_Image_Cleanup;
$productImageCleanup->run();
