<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$currentConfig = Mage::getStoreConfig('cloudinary/setup/cloudinary_enabled');

if (is_null($currentConfig)) {
    $legacyConfig = Mage::getStoreConfig('cloudinary/cloud/cloudinary_enabled');
    Mage::getModel('core/config')->saveConfig('cloudinary/setup/cloudinary_enabled', $legacyConfig)->reinit();
}

$installer->endSetup();
