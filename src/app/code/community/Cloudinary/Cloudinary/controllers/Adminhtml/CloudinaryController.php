<?php

class Cloudinary_Cloudinary_Adminhtml_CloudinaryController extends Mage_Adminhtml_Controller_Action
{
    private $_migrationTask;

    private $_cloudinaryConfig;

    public function preDispatch()
    {
        $this->_migrationTask = Mage::getModel('cloudinary_cloudinary/migration')->load(Cloudinary_Cloudinary_Model_Cron::CLOUDINARY_MIGRATION_ID);
        $this->_cloudinaryConfig = Mage::helper('cloudinary_cloudinary/configuration');

        parent::preDispatch();
    }

    public function indexAction()
    {
        $configBlock = $this->getLayout()->createBlock('cloudinary_cloudinary/adminhtml_manage');
        $progressBlock = $this->getLayout()->createBlock('core/text')->setText('Progress: %0....');

        $configBlock
            ->setMigrationStarted($this->_migrationTask->hasStarted())
            ->setExtensionEnabled($this->_cloudinaryConfig->isEnabled())
            ->build();

        $this->loadLayout()
            ->_addContent($configBlock)
            ->_addContent($progressBlock)
            ->renderLayout();
    }

    public function startMigrationAction()
    {
        $this->_migrationTask->start();

        $this->_redirect('*/cloudinary');
    }

    public function stopMigrationAction()
    {
        $this->_migrationTask->stop();

        $this->_redirect('*/cloudinary');
    }

    public function enableCloudinaryAction()
    {
        $this->_cloudinaryConfig->enable();

        $this->_redirect('*/cloudinary');
    }

    public function disableCloudinaryAction()
    {
        $this->_cloudinaryConfig->disable();

        $this->_redirect('*/cloudinary');
    }
}