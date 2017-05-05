<?php

use CloudinaryExtension\Migration\SynchronizedMediaRepository;

class Cloudinary_Cloudinary_Model_Resource_Cms_Synchronisation_Collection
    extends Mage_Cms_Model_Wysiwyg_Images_Storage_Collection
    implements SynchronizedMediaRepository
{
    /**
     * @var string[]
     * @link http://cloudinary.com/documentation/image_transformations#format_conversion
     * @link http://cloudinary.com/documentation/upload_images
     */
    private $allowedImgExtensions = ['JPG', 'PNG', 'GIF', 'BMP', 'TIFF', 'EPS', 'PSD', 'SVG', 'WebP'];

    public function __construct()
    {
        $categoryImages = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category';
        $this->addTargetDir($categoryImages);
        $this->addTargetDir(Mage::getBaseDir('media').DS.Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY);
        $this->setItemObjectClass('cloudinary_cloudinary/cms_synchronisation');
        $this->setFilesFilter(
            sprintf('#^[a-z0-9\.\-\_]+\.(?:%s)$#i', implode('|', $this->allowedImgExtensions))
        );
    }

    public function addTargetDir($value)
    {
        try {
            parent::addTargetDir($value);
        } catch (Exception $e) {
            Mage::logException($e);
            if (!Mage::registry('error_' . $value)) {
                Mage::getSingleton('core/session')->addError("Couldn't find path " . $value);
                Mage::register('error_' . $value, true);
            }
            throw $e;
        }
    }

    public function findUnsynchronisedImages()
    {
        $helperConfig = Mage::getModel('cloudinary_cloudinary/configuration');
        if ($helperConfig->isFolderedMigration()){
            $this->addFieldToFilter('filename', array('nin' => $this->_getSynchronisedImageNames()));
        } else {
            $this->addFieldToFilter('basename', array('nin' => $this->_getSynchronisedImageNames()));
        }

        return $this->getItems();
    }

    private function _getSynchronisedImageNames()
    {
        $result = array_map(
            function ($itemData) {
                $imageName = $itemData['image_name'];
                return Mage::getModel('cloudinary_cloudinary/configuration')->reverseMigratedPathIfNeeded($imageName);
            },
            $this->_getSynchronisedImageData()
        );

        return $result;
    }

    private function _getSynchronisedImageData()
    {
        return Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection')
            ->addFieldToSelect('image_name')
            ->addFieldToFilter('media_gallery_id', array('null' => true))
            ->getData();
    }
}
