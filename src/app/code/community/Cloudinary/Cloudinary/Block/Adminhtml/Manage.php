<?php

class Cloudinary_Cloudinary_Block_Adminhtml_Manage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    private $_migrationTask;

    private $_cloudinaryConfig;

    public function __construct()
    {
        $this->_blockGroup = 'cloudinary_cloudinary';

        $this->_controller = 'adminhtml_manage';

        $this->_headerText = Mage::helper('cloudinary_cloudinary')
            ->__('Manage Cloudinary');

        $this->_migrationTask = Mage::getModel('cloudinary_cloudinary/migration')
            ->load(Cloudinary_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID);

        $this->_cloudinaryConfig = Mage::helper('cloudinary_cloudinary/configuration');

        parent::__construct();
    }

    public function getPercentComplete()
    {
        return $this->getSynchronizedImageCount() * 100 / $this->getTotalImageCount();
    }

    public function getSynchronizedImageCount()
    {
        return Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection')->getSize();
    }

    public function getTotalImageCount()
    {
        $mediaCounter = Mage::getModel('cloudinary_cloudinary/mediaCollectionCounter')
            ->addCollection(Mage::getResourceModel('cloudinary_cloudinary/media_collection'))
            ->addCollection(Mage::getResourceModel('cloudinary_cloudinary/cms_synchronisation_collection'));

        return $mediaCounter->count();
    }

    public function hasMigrationStarted()
    {
        return $this->_migrationTask->hasStarted();
    }

    public function isExtensionEnabled()
    {
        return $this->_cloudinaryConfig->isEnabled();
    }

    public function allImagesSynced()
    {
        return $this->getSynchronizedImageCount() === $this->getTotalImageCount();
    }
} 