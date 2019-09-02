<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$con = $installer->getConnection();
$table = $installer->getTable('cloudinary_cloudinary/migration');

$con->dropTable($table);

$table = $con->newTable($table)
    ->addColumn(
        'cloudinary_migration_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'auto_increment' => true,
        ), 'Cloudinary Migration ID'
    )
    ->addColumn(
        'started', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default' => 0,
        ), 'Migration Started'
    )
    ->addColumn(
        'started_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'comment' => 'The time the migration started',
        'nullable' => true,
        'default' => '0000-00-00 00:00:00'
        )
    )->addColumn(
        'batch_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'comment' => 'Batches run for current migration',
            'nullable' => false,
            'default' => 0
            )
    )->addColumn(
        'type', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
            'comment' => 'Migration Type',
            'nullable' => true,
            'default' => 'upload'
            )
    )->addColumn(
        'info', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'comment' => 'Info',
            'nullable' => true,
            //'default' => '[]'
            )
    );

$con->createTable($table);

$con->addIndex(
    $installer->getTable('cloudinary_cloudinary/migration'),
    $installer->getIdxName(
        'cloudinary_cloudinary/migration',
        array('type'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('type'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$con->addColumn(
    $installer->getTable('cloudinary_cloudinary/migrationError'), 'type', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'nullable'  => true,
    'length'    => 10,
    'comment'   => 'Migration Type',
    'default' => 'upload'
    )
);

$installer->endSetup();
