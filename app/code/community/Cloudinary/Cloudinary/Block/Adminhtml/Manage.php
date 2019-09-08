<?php

class Cloudinary_Cloudinary_Block_Adminhtml_Manage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * @var Cloudinary_Cloudinary_Model_Migration
     */
    private $_migrationTask;

    private $_cloudinaryConfig;

    public function __construct()
    {
        $this->_blockGroup = 'cloudinary_cloudinary';
        $this->_controller = 'adminhtml_manage';
        $this->_headerText = Mage::helper('cloudinary_cloudinary')->__('Manage Cloudinary');
        $this->_migrationTask = Mage::getModel('cloudinary_cloudinary/migration')->loadType($this->getType());
        $this->_cloudinaryConfig = Mage::getModel('cloudinary_cloudinary/configuration');
        parent::__construct();
    }

    public function getType()
    {
        return Mage::registry('cloudinary_migration_type');
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

    public function isAutoUploadMappingEnabled()
    {
        return $this->_cloudinaryConfig->hasAutoUploadMapping();
    }

    public function allImagesSynced()
    {
        try {
            return
                ($type === Cloudinary_Cloudinary_Model_Migration::UPLOAD_MIGRATION_TYPE && $this->getSynchronizedImageCount() === $this->getTotalImageCount()) ||
                ($type === Cloudinary_Cloudinary_Model_Migration::DOWNLOAD_MIGRATION_TYPE && !$this->getMigrationInfo()->getData('more_expected') && $this->getMigrationInfo()->getData('resources_count_total') && $this->getMigrationInfo()->getData('resources_processed_total') === $this->getMigrationInfo()->getData('resources_count_total'));
        } catch (Exception $e) {
            return false;
        }
    }

    public function getMigrationInfo()
    {
        if (!$this->hasData('migration_info')) {
            $this->setData('migration_info', new Varien_Object($this->_migrationTask->getInfo()));
        }

        return $this->getData('migration_info');
    }

    public function getMigrateButton()
    {
        $type = $this->getType();
        if ($this->_migrationTask->hasStarted()) {
            $startLabel = 'Stop Migration';
            $startAction = 'stopMigration';
            return $this->_makeButton($startLabel, $startAction, $type === Cloudinary_Cloudinary_Model_Migration::UPLOAD_MIGRATION_TYPE && $this->allImagesSynced());
        }

        return $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                'id' => 'cloudinary_migration_start_' . $type,
                'label' => $this->helper('adminhtml')->__('Start Migration'),
                'disabled' => $type === Cloudinary_Cloudinary_Model_Migration::UPLOAD_MIGRATION_TYPE && $this->allImagesSynced(),
                'onclick' => 'openCloudinaryMigrationPopup(\''.$type.'\');'
                )
            )
            ->toHtml();
    }

    public function getStartMigrationUrl()
    {
        return $this->getUrl('*/cloudinary/startMigration/type/' . $this->getType());
    }

    public function getClearErrorsButton()
    {
        $areThereErrors = $this->getErrors();
        return $this->_makeButton($areThereErrors ? 'Clear errors' : 'No errors to clear', 'clearErrors', !$areThereErrors);
    }

    private function _makeButton($label, $action, $disabled = false)
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                'id' => 'cloudinary_migration_start_' . $action,
                'label' => $this->helper('adminhtml')->__($label),
                'disabled' => $disabled,
                'onclick' => "setLocation('{$this->getUrl(sprintf('*/cloudinary/%s/type/%s', $action, $this->getType()))}')"
                )
            );

        return $button->toHtml();
    }

    public function getCloudinaryConfigurationLink()
    {
        return Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/cloudinary");
    }

    public function getErrors()
    {
        return Mage::getModel('cloudinary_cloudinary/migrationError')->getCollection()
            ->addFieldToFilter('type', $this->getType())
            ->addOrder('timestamp')
            ->getItems();
    }
}
