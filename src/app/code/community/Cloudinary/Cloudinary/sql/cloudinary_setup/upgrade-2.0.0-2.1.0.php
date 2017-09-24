<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$conn = $installer->getConnection();
$table = $installer->getTable('cloudinary_cloudinary/migration');

$conn->addColumn(
    $table,
    'started_at',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment' => 'The time the migration started',
        'nullable' => true,
        'default' => '0000-00-00 00:00:00'
    ]
);

$conn->addColumn(
    $table,
    'batch_count',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Batches run for current migration',
        'nullable' => false,
        'default' => 0
    ]
);

$installer->endSetup();
