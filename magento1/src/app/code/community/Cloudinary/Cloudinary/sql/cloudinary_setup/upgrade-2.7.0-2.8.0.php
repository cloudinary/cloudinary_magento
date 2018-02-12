<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('cloudinary_cloudinary/transformation'))
    ->addColumn(
        'image_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable'  => false,
            'primary'   => true
        ),
        'Relative image path'
    )
    ->addColumn(
        'free_transformation',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(),
        'Free transformation'
    );

$installer->getConnection()->createTable($table);

$installer->endSetup();
