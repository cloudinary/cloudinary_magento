<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('cloudinary_cloudinary/migrationError'))
    ->addColumn('file_path', Varien_Db_Ddl_Table::TYPE_BINARY, 255, array(
        'primary' => true,
        'nullable' => false
    ), 'File path')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_INTEGER, null)
    ->addColumn('relative_path', Varien_Db_Ddl_Table::TYPE_BINARY, 255)
    ->addColumn('timestamp', Varien_Db_Ddl_Table::TYPE_DATETIME, null);

print_r($installer->getConnection()->createTable($table));
$installer->endSetup();
