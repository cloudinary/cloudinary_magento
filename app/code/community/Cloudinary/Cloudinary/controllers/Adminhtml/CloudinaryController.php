<?php

class Cloudinary_Cloudinary_Adminhtml_CloudinaryController extends Mage_Adminhtml_Controller_Action
{
    const CRON_INTERVAL = 300;
    const MIGRATION_START_MESSAGE = 'migration start requested.';
    const MIGRATION_STOP_MESSAGE = 'migration stop requested.';
    const MIGRATION_CRON_WARNING = 'cron is not running, so no migration will occur.';

    /**
     * @var Cloudinary_Cloudinary_Model_Migration
     */
    private $_migrationTask;

    /**
     * @var Cloudinary_Cloudinary_Helper_Configuration
     */
    private $_cloudinaryConfig;

    private function configurePage()
    {
        $this->_title($this->__('Manual Migration'))
            ->_setActiveMenu('cloudinary_cloudinary/manage');
    }

    public function preDispatch()
    {
        $this->_migrationTask = Mage::getModel('cloudinary_cloudinary/migration')->loadType($this->getType());
        $this->_cloudinaryConfig = Mage::getModel('cloudinary_cloudinary/configuration');
        parent::preDispatch();
    }

    public function getType()
    {
        switch ($this->getRequest()->getParam('type')) {
            case Cloudinary_Cloudinary_Model_Migration::UPLOAD_MIGRATION_TYPE:
                return Cloudinary_Cloudinary_Model_Migration::UPLOAD_MIGRATION_TYPE;
                break;
            case Cloudinary_Cloudinary_Model_Migration::DOWNLOAD_MIGRATION_TYPE:
                return Cloudinary_Cloudinary_Model_Migration::DOWNLOAD_MIGRATION_TYPE;
                break;
            default:
                throw new Mage_Core_Exception(__('Cloudinary Error: Wrong migration type.'));
                break;
        }
    }

    protected function indexAction()
    {
        Mage::register('cloudinary_migration_type', $this->getType());

        $this->removeOrphanSyncEntries();

        $this->_displayMigrationMessages();

        $layout = $this->loadLayout();
        $this->configurePage();

        if (!$this->_cloudinaryConfig->validateCredentials()) {
            $this->_displayValidationFailureMessage();
        }

        if ($this->_migrationTask->hasStarted()) {
            $layout->_addContent($this->_buildMetaRefreshBlock());
        }

        $cronMigrationValid = Mage::helper('cloudinary_cloudinary/cron')
            ->validate($this->_migrationTask, self::CRON_INTERVAL);

        if (!$cronMigrationValid) {
            $this->_displayCronFailureMessage();
        }

        $this->renderLayout();
    }

    public function removeOrphanSyncEntries()
    {
        $combinedMediaRepository = Mage::getModel(
            'cloudinary_cloudinary/synchronisedMediaUnifier',
            array(
                Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection'),
                Mage::getResourceModel('cloudinary_cloudinary/cms_synchronisation_collection')
            )
        );

        foreach ($combinedMediaRepository->findOrphanedSynchronisedImages() as $orphanImage) {
            $error = Mage::getModel('cloudinary_cloudinary/migrationError')->orphanRemoved($orphanImage);
            Mage::getModel('cloudinary_cloudinary/logger')->notice($error->getMessage());
            $error->save();
            $orphanImage->delete();
        }
    }

    public function configAction()
    {
        $this->_redirect("*/system_config/edit/section/cloudinary/");
    }

    public function startMigrationAction()
    {
        $this->_migrationTask->start();

        Mage::getModel('cloudinary_cloudinary/logger')->notice(self::MIGRATION_START_MESSAGE);

        $this->_redirectToManageCloudinary();
    }

    public function stopMigrationAction()
    {
        $this->_migrationTask->stop();

        Mage::getModel('cloudinary_cloudinary/logger')->notice(self::MIGRATION_STOP_MESSAGE);

        $this->_redirectToManageCloudinary();
    }

    public function clearErrorsAction()
    {
        $items = Mage::getModel('cloudinary_cloudinary/migrationError')->getCollection()
            ->addFieldToFilter('type', $this->getType())
            ->getItems();

        foreach ($items as $error) {
            $error->delete();
        }

        $this->_redirectToManageCloudinary();
    }

    private function _displayMigrationMessages()
    {
        if ($this->_migrationTask->hasStarted()) {
            $cron = Mage::helper('cloudinary_cloudinary/cron');

            if (!$cron->validate($this->_migrationTask, self::CRON_INTERVAL)) {
                $this->_displayCronFailureMessage();
            } elseif ($cron->isInitialising($this->_migrationTask)) {
                $this->_displayCronInitialisingMessage();
            }
        }
    }

    private function _redirectToManageCloudinary()
    {
        return $this->_redirect('*/cloudinary/index/type/' . $this->getType());
    }

    private function _buildMetaRefreshBlock()
    {
        return $this->getLayout()->createBlock('core/text')->setText('<meta http-equiv="refresh" content="5">');
    }

    private function _displayCronInitialisingMessage()
    {
        $this->_getSession()->addNotice('Initializing migration, please wait.');
    }

    private function _displayCronFailureMessage()
    {
        $this->_getSession()->addError(
            sprintf(
                '%s You can find details how to enable cron <a href="%s" target="_blank">here</a>',
                'Error: cron is not running, so no migration will occur.',
                'https://support.cloudinary.com/hc/en-us/articles/203188781-Why-is-the-migration-process-stuck-on-zero-'
            )
        );

        Mage::getModel('cloudinary_cloudinary/logger')->error(self::MIGRATION_CRON_WARNING);
    }

    private function _displayValidationFailureMessage()
    {
        $this->_getSession()->addError(
            sprintf(
                'Please enter your Cloudinary Credentials <a href="%s">here</a> to Activate Cloudinary',
                Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/cloudinary")
            )
        );
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cloudinary_cloudinary/cloudinary');
    }
}
