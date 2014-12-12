<?php

class Cloudinary_Cloudinary_Block_Adminhtml_Manage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'cloudinary_cloudinary';

        $this->_controller = 'adminhtml_manage';

        $this->_headerText = Mage::helper('cloudinary_cloudinary')
            ->__('Manage Cloudinary');

        parent::__construct();

        $this->_removeButton('add');
    }

    public function build()
    {

        if ($this->getExtensionEnabled()) {
            $enableLabel = 'Disable Cloudinary';
            $enableAction = 'disableCloudinary';
        } else {
            $enableLabel = 'EnableCloudinary';
            $enableAction = 'enableCloudinary';
        }

        if ($this->getMigrationStarted()) {
            $startLabel = 'Stop Migration';
            $startAction = 'stopMigration';
        } else {
            $startLabel = 'Start Migration';
            $startAction = 'startMigration';
        }

        $noImagesToSync = (0 === $this->getSynchronizedImageCount() - $this->getImageCount());

        $this->_addButton('cloudinary_migration_start', array(
            'label' => $this->__($startLabel),
            'disabled' => $noImagesToSync,
            'onclick' => "setLocation('{$this->getUrl(sprintf('*/cloudinary/%s', $startAction))}')",
        ));

        $this->_addButton('cloudinary_toggle_enable', array(
            'label' => $this->__($enableLabel),
            'onclick' => "setLocation('{$this->getUrl(sprintf('*/cloudinary/%s', $enableAction))}')",
        ));
    }
} 