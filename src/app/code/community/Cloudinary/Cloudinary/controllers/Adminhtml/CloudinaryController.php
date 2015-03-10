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
        $layout = $this->loadLayout();

        if ($this->_migrationTask->hasStarted()) {
            $layout->_addContent($this->_buildMetaRefreshBlock());
        }

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

    private function redirect()
    {
        return $this->_redirect('*/cloudinary');
    }

    private function _buildMetaRefreshBlock()
    {
        return $this->getLayout()->createBlock('core/text')->setText('<meta http-equiv="refresh" content="5">');
    }

}