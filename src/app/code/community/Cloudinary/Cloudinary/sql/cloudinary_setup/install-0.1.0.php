<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('cloudinary_cloudinary/synchronisation'))
    ->addColumn('cloudinary_synchronisation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Cloudinary Synchronisation ID')
    ->addColumn('media_gallery_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
        'default' => null,
    ), 'Media Gallery ID')
    ->addColumn('image_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('in_cloudinary', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ), 'Media in Cloudinary')
    ->addForeignKey(
        'FK_MEDIA_GALLERY_ID_VALUE_ID',
        'media_gallery_id',
        $installer->getTable('catalog_product_entity_media_gallery'),
        'value_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

$installer->endSetup();