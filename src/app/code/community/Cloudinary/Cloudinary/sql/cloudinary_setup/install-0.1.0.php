<?php

/* @var $installer  Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('cloudinary_cloudinary/synchronisation'))
    ->addColumn('media_gallery_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Media Gallery ID')
    ->addColumn('in_cloudinary', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ), 'Media in Cloudinary');
//    ->addForeignKey(
//        'FK_MEDIA_GALLERY_ID_VALUE_ID',
//        'media_gallery_id',
//        $installer->getTable('cloudinary_cloudinary/synchronisation'),
//        'value_id',
//        $installer->getTable('catalog_product_entity_media_gallery'),
//        'cascade',
//        'cascade'
//    );
$installer->getConnection()->createTable($table);

$installer->endSetup();