<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('cloudinary_cloudinary/migration'))
    ->addColumn('cloudinary_migration_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => Cloudinary_Cloudinary_Model_Cron::CLOUDINARY_MIGRATION_ID
    ), 'Cloudinary Migration ID')
    ->addColumn('started', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default' => 0,
    ), 'Migration Started');

$installer->getConnection()->createTable($table);

$installer->endSetup();