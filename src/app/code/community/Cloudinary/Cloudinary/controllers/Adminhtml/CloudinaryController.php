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
        $totalImageCount = Mage::getResourceModel('cloudinary_cloudinary/media_collection')->getSize();
        $totalSynchronizedImageCount = Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection')->getSize();
        $totalUnsynchronizedImageCount = $totalImageCount - $totalSynchronizedImageCount;

        $progressBlock = $this->_buildProgressBlock($totalSynchronizedImageCount, $totalImageCount);
        $configBlock = $this->_buildConfigBlock($totalUnsynchronizedImageCount);

        $layout = $this->loadLayout();
        $layout->_addContent($configBlock);

        if ($this->_migrationTask->hasStarted()) {
            $layout->_addContent($progressBlock);
        }

        $this->renderLayout();
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

    private function _buildConfigBlock($totalUnsynchronizedImageCount)
    {
        $configBlock = $this->getLayout()->createBlock('cloudinary_cloudinary/adminhtml_manage');
        $configBlock
            ->setMigrationStarted($this->_migrationTask->hasStarted())
            ->setExtensionEnabled($this->_cloudinaryConfig->isEnabled())
            ->setTotalUnsychronizedCount($totalUnsynchronizedImageCount)
            ->build();

        return $configBlock;
    }

    private function _buildProgressBlock($totalSynchronizedImageCount, $totalImageCount)
    {
        $percentComplete = number_format($totalSynchronizedImageCount * 100 / $totalImageCount, 2);

        $progressBlock = $this->getLayout()
            ->createBlock('core/text')
            ->setText(
                '<p>Progress: ' . $percentComplete . '%</p>' .
                '<p>' . $totalSynchronizedImageCount . ' of ' . $totalImageCount . ' images migrated.</p>'
            );
        return $progressBlock;
    }
}