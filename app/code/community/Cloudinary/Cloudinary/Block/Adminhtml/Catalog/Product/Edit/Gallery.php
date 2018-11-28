<?php

class Cloudinary_Cloudinary_Block_Adminhtml_Catalog_Product_Edit_Gallery extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->_blockGroup = 'cloudinary_cloudinary';
        $this->_controller = 'adminhtml_cloudinaryproduct';
        $this->_headerText = Mage::helper('cloudinary_cloudinary')->__('Cloudinary ');
        parent::__construct();
    }

    /**
     * @return bool
     */
    public function isCloudinaryEnabled()
    {
        return Mage::getModel('cloudinary_cloudinary/configuration')->isEnabled();
    }

    /**
     * @return string
     */
    public function getCloudinaryConfigurationLink()
    {
        return Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/cloudinary");
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->injectFreeTransformations(
            $this->convertImagesToObjects(
                $this->getMediaGallery($this->getProduct())
            )
        );
    }

    /**
     * @return string
     */
    public function ajaxSampleSecretKey()
    {
        return Mage::getModel('adminhtml/url')->getSecretKey('cloudinaryajax', 'sample');
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product') ?: $this->loadProductFromRequest();
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    private function loadProductFromRequest()
    {
        return Mage::getModel('catalog/product')->load(Mage::app()->getRequest()->getParam('id'));
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    private function getMediaGallery(Mage_Catalog_Model_Product $product)
    {
        $gallery = $product->getMediaGallery();

        if (!$gallery || !is_array($gallery) || !array_key_exists('images', $gallery)) {
            return array();
        }

        return $gallery['images'];
    }

    /**
     * @param array $images
     * @return [Varien_Object]
     */
    private function convertImagesToObjects(array $images)
    {
        return array_map(
            function(array $image) {
                $object = new Varien_Object();
                return $object->setData($image);
            },
            $images
        );
    }

    /**
     * @param [Varien_Object] $images
     * @return [Varien_Object]
     */
    private function injectFreeTransformations(array $images)
    {
        return array_map(
            function(Varien_Object $image) {
                $model = Mage::getModel('cloudinary_cloudinary/transformation');
                $model->load($image->getFile());
                $image->setFreeTransformation($model->getFreeTransformation());
                return $image;
            },
            $images
        );
    }
}
