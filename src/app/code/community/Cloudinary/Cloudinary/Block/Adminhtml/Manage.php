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

    public function isFolderedMigration()
    {
        return $this->_cloudinaryConfig->isFolderedMigration();
    }

    public function getPercentComplete()
    {
        try {
            if ($this->getTotalImageCount() != 0) {
                return $this->getSynchronizedImageCount() * 100 / $this->getTotalImageCount();
            }
        } catch (Exception $e) {
            return 'Unknown';
        }
    }

    public function getSynchronizedImageCount()
    {
        return Mage::getResourceModel('cloudinary_cloudinary/synchronisation_collection')->getSize();
    }

    public function getTotalImageCount()
    {
        try {
            $collectionCounter = Mage::getModel('cloudinary_cloudinary/collectionCounter')
                ->addCollection(Mage::getResourceModel('cloudinary_cloudinary/cms_synchronisation_collection'));
            $result = $collectionCounter->count();
        } catch (Exception $e) {
            return 'Unknown';
        }

        $result += Mage::getResourceModel('cloudinary_cloudinary/media_collection')->uniqueImageCount();
        return $result;
    }

    public function isExtensionEnabled()
    {
        return $this->_cloudinaryConfig->isEnabled();
    }

    public function allImagesSynced()
    {
        try {
            return $this->getSynchronizedImageCount() === $this->getTotalImageCount();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getEnableButton()
    {
        if ($this->_cloudinaryConfig->isEnabled()) {
            $enableLabel = 'Disable Cloudinary';
            $enableAction = 'disableCloudinary';
        } else {
            $enableLabel = 'Enable Cloudinary';
            $enableAction = 'enableCloudinary';
        }

        return $this->_makeButton($enableLabel, $enableAction);
    }

    public function getMigrateButton()
    {
        if ($this->_migrationTask->hasStarted()) {
            $startLabel = 'Stop Migration';
            $startAction = 'stopMigration';
        } else {
            $startLabel = 'Start Migration';
            $startAction = 'startMigration';
        }

        return $this->_makeButton($startLabel, $startAction, $this->allImagesSynced());
    }

    public function getClearErrorsButton()
    {
        $areThereErrors = $this->getErrors();
        return $this->_makeButton($areThereErrors ? 'Clear errors' : 'No errors to clear', 'clearErrors', !$areThereErrors);
    }

    private function _makeButton($label, $action, $disabled = false)
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'cloudinary_migration_start',
                'label' => $this->helper('adminhtml')->__($label),
                'disabled' => $disabled,
                'onclick' => "setLocation('{$this->getUrl(sprintf('*/cloudinary/%s', $action))}')"
            ));

        return $button->toHtml();
    }

    public function getErrors(){
        $coll = Mage::getModel('cloudinary_cloudinary/migrationError')->getCollection();
        $coll->addOrder('timestamp');
        return $coll->getItems();
    }
}
