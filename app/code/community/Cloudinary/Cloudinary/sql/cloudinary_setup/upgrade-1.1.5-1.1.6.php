<?php

$installer = $this;
/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$installer->getConnection()->addIndex(
    $installer->getTable('cloudinary_synchronisation'),
    $installer->getIdxName('cloudinary_synchronisation', array('image_name')),
    array('image_name')
);

$installer->endSetup();
