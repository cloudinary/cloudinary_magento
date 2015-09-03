<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$synchronizationTable = $installer->getTable('cloudinary_cloudinary/synchronisation');

$options = [
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'The name with which the image can be found in the product related media gallery table'
];

$result = $conn->addColumn($synchronizationTable, 'media_gallery_value', $options);
print_r($result);

$installer->endSetup();
