<?php

class Cloudinary_Cloudinary_Adminhtml_CloudinaryController extends Mage_Adminhtml_Controller_Action
{
    private $_migrationTask;

    private $_cloudinaryConfig;

    public function preDispatch()
    {
        $this->_migrationTask = Mage::getModel('cloudinary_cloudinary/migration')->load(Cloudinary_Cloudinary_Model_Migration::CLOUDINARY_MIGRATION_ID);
        $this->_cloudinaryConfig = Mage::helper('cloudinary_cloudinary/configuration');

        parent::preDispatch();
    }

    public function indexAction()
    {
//        $totalImageCount = $this->_sumOfCatalogAndCmsImages();
//        $totalSynchronizedImageCount = $this->_synchronisedImageCount();
//
//        $progressBlock = $this->_buildProgressBlock($totalSynchronizedImageCount, $totalImageCount);
//        $configBlock = $this->_buildConfigBlock($totalSynchronizedImageCount, $totalImageCount);
//
//        if ($totalImageCount === $totalSynchronizedImageCount) {
//            Mage::getSingleton('core/session')->addNotice('All images have been successfully migrated to Cloudinary');
//        }
//
        $layout = $this->loadLayout();
//        $layout->_addContent($configBlock);
//        $layout->_addContent($this->_buildWarningBlock());
//
//        if ($this->_migrationTask->hasStarted()) {
//            $layout->_addContent($progressBlock);
//            $layout->_addContent($this->_buildMetaRefreshBlock());
//        }

        $this->renderLayout();
    }

    public function startMigrationAction()
    {
        $this->_migrationTask->start();

        $this->redirect();
    }

    public function stopMigrationAction()
    {
        $this->_migrationTask->stop();

        $this->redirect();
    }

    public function enableCloudinaryAction()
    {
        $this->_cloudinaryConfig->enable();

        $this->redirect();
    }

    public function disableCloudinaryAction()
    {
        $this->_cloudinaryConfig->disable();

        $this->redirect();
    }

    private function _buildConfigBlock($totalSynchronizedImageCount, $totalImageCount)
    {

        $configBlock = $this->getLayout()->createBlock('cloudinary_cloudinary/adminhtml_manage');
        $configBlock->setMigrationStarted($this->_migrationTask->hasStarted())
            ->setExtensionEnabled($this->_cloudinaryConfig->isEnabled())
            ->setImageCount($totalImageCount)
            ->setSynchronizedImageCount($totalSynchronizedImageCount)
            ->build();

        return $configBlock;
    }

    private function _buildProgressBlock($totalSynchronizedImageCount, $totalImageCount)
    {
        return $this->getLayout()->createBlock('cloudinary_cloudinary/adminhtml_progress')
            ->setImageCount($totalImageCount)
            ->setSynchronizedImageCount($totalSynchronizedImageCount)
            ->build();
    }

    private function _buildWarningBlock()
    {
//        return $this->getLayout()->createBlock('cloudinary_cloudinary/adminhtml_warning')
//            ->build();
    }

    private function redirect()
    {
        return $this->_redirect('*/cloudinary');
    }

    private function _buildMetaRefreshBlock()
    {
        return $this->getLayout()->createBlock('core/text')->setText('<meta http-equiv="refresh" content="5">');
    }

    private function _sumOfCatalogAndCmsImages()
    {
        $mediaCounter = Mage::getModel('cloudinary_cloudinary/mediaCollectionCounter')
            ->addCollection(Mage::getResourceModel('cloudinary_cloudinary/media_collection'))
            ->addCollection(Mage::getResourceModel('cloudinary_cloudinary/cms_synchronisation_collection'));

        return $mediaCounter->count();
    }

    private function _synchronisedImageCount()
    {
        return Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection')->getSize();
    }
}